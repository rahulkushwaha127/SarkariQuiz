<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'best_streak',
        'last_streak_date',
        'total_xp',
        'level',
    ];

    protected $casts = [
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'last_streak_date' => 'date',
        'total_xp' => 'integer',
        'level' => 'integer',
    ];

    /**
     * XP thresholds for each level.
     */
    public const LEVELS = [
        1 => 0,
        2 => 100,
        3 => 300,
        4 => 600,
        5 => 1000,
        6 => 1500,
        7 => 2200,
        8 => 3000,
        9 => 4000,
        10 => 5500,
    ];

    public const LEVEL_NAMES = [
        1 => 'Beginner',
        2 => 'Learner',
        3 => 'Rising Star',
        4 => 'Skilled',
        5 => 'Pro',
        6 => 'Expert',
        7 => 'Master',
        8 => 'Champion',
        9 => 'Legend',
        10 => 'Grandmaster',
    ];

    public function levelName(): string
    {
        return self::LEVEL_NAMES[$this->level] ?? 'Beginner';
    }

    public function xpForNextLevel(): int
    {
        $next = $this->level + 1;
        return self::LEVELS[$next] ?? self::LEVELS[10];
    }

    public function xpProgress(): int
    {
        $current = self::LEVELS[$this->level] ?? 0;
        $next = $this->xpForNextLevel();
        $range = $next - $current;
        if ($range <= 0) return 100;
        return (int) min(100, round(($this->total_xp - $current) * 100 / $range));
    }

    public static function computeLevel(int $totalXp): int
    {
        $level = 1;
        foreach (self::LEVELS as $lvl => $threshold) {
            if ($totalXp >= $threshold) {
                $level = $lvl;
            }
        }
        return $level;
    }

    /**
     * Award XP and update streak for a user. Call after any quiz/practice/pyq submission.
     * Returns ['xp_earned' => int, 'leveled_up' => bool, 'new_level' => int].
     */
    public static function awardXp(int $userId, int $correctCount): array
    {
        $xpEarned = 10 + ($correctCount * 2);
        $leveledUp = false;
        $newLevel = 1;
        $date = now()->toDateString();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $date, $xpEarned, &$leveledUp, &$newLevel) {
                $row = self::query()->firstOrNew(['user_id' => $userId]);
                $oldLevel = (int) ($row->level ?? 1);

                // Streak: one increment per calendar day
                if (!$row->last_streak_date || $row->last_streak_date->toDateString() !== $date) {
                    $prev = \Illuminate\Support\Carbon::parse($date)->subDay()->toDateString();
                    $current = (int) ($row->current_streak ?? 0);

                    if ($row->last_streak_date && $row->last_streak_date->toDateString() === $prev) {
                        $current = $current + 1;
                    } else {
                        $current = 1;
                    }

                    $row->current_streak = $current;
                    $row->best_streak = max((int) ($row->best_streak ?? 0), $current);
                    $row->last_streak_date = $date;
                }

                // XP
                $row->total_xp = (int) ($row->total_xp ?? 0) + $xpEarned;
                $row->level = self::computeLevel($row->total_xp);
                $newLevel = $row->level;
                $leveledUp = $row->level > $oldLevel;

                $row->save();
            });
        } catch (\Throwable $e) {
            // Never break the submit flow
        }

        return [
            'xp_earned' => $xpEarned,
            'leveled_up' => $leveledUp,
            'new_level' => $newLevel,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

