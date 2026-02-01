<?php

namespace App\Console\Commands;

use App\Models\DailyChallenge;
use App\Models\FcmToken;
use App\Models\NotificationSendLog;
use App\Services\Notifications\FcmSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendDailyQuizReminder extends Command
{
    protected $signature = 'notifications:daily-quiz
        {--date= : YYYY-MM-DD (default: today)}
        {--dry-run : Do not send, only show counts}';

    protected $description = 'Send the daily quiz reminder via FCM (login-only)';

    public function handle(FcmSender $sender): int
    {
        $date = (string) ($this->option('date') ?: now()->toDateString());
        $dryRun = (bool) $this->option('dry-run');

        $daily = DailyChallenge::query()
            ->where('challenge_date', $date)
            ->where('is_active', true)
            ->with('quiz')
            ->first();

        if (! $daily?->quiz) {
            $this->info("No active daily challenge for {$date}.");
            return self::SUCCESS;
        }

        $uniqueKey = "daily_quiz_reminder:{$date}";

        if (NotificationSendLog::query()->where('unique_key', $uniqueKey)->exists()) {
            $this->info("Already sent: {$uniqueKey}");
            return self::SUCCESS;
        }

        $tokens = FcmToken::query()
            ->from('fcm_tokens as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->whereNull('t.revoked_at')
            ->whereNull('u.blocked_at')
            ->where('u.is_guest', false)
            ->select('t.token')
            ->pluck('token')
            ->unique()
            ->values()
            ->all();

        $payload = [
            'priority' => 'high',
            'notification' => [
                'title' => 'Daily Challenge',
                'body' => $daily->quiz->title,
            ],
            'data' => [
                'type' => 'daily_quiz_reminder',
                'url' => route('student.daily'),
                'date' => $date,
                'quiz_code' => $daily->quiz->unique_code,
            ],
        ];

        $this->info("Daily challenge: {$daily->quiz->title}");
        $this->info('Recipients (tokens): ' . count($tokens));

        if ($dryRun) {
            $this->warn('Dry run: not sending.');
            return self::SUCCESS;
        }

        // Ensure idempotency even if two schedulers run.
        DB::transaction(function () use ($uniqueKey, $payload, $tokens) {
            NotificationSendLog::query()->create([
                'type' => 'daily_quiz_reminder',
                'unique_key' => $uniqueKey,
                'payload' => $payload,
                'recipient_count' => count($tokens),
                'sent_at' => now(),
            ]);
        });

        $success = 0;
        $failure = 0;
        $errors = [];

        foreach (array_chunk($tokens, 500) as $chunk) {
            $res = $sender->sendToTokens($chunk, $payload);
            if (!($res['ok'] ?? false)) {
                $errors[] = $res['error'] ?? 'Unknown error';
                continue;
            }
            $success += (int) ($res['success'] ?? 0);
            $failure += (int) ($res['failure'] ?? 0);
        }

        if (count($errors) > 0) {
            $this->error('Errors: ' . implode(' | ', array_unique($errors)));
            return self::FAILURE;
        }

        $this->info("Sent. Success: {$success}, Failure: {$failure}");
        return self::SUCCESS;
    }
}

