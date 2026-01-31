<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_user_id',
        'quiz_id',
        'title',
        'description',
        'join_mode',
        'join_code',
        'status',
        'starts_at',
        'ends_at',
        'is_public_listed',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public_listed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $contest) {
            if (in_array($contest->join_mode, ['code', 'link'], true) && ! $contest->join_code) {
                $contest->join_code = self::generateJoinCode();
            }
        });
    }

    public static function generateJoinCode(int $length = 6): string
    {
        $length = max(4, min(12, $length));

        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function participants()
    {
        return $this->hasMany(ContestParticipant::class);
    }
}

