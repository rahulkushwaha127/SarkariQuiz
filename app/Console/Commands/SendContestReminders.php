<?php

namespace App\Console\Commands;

use App\Models\Contest;
use App\Models\FcmToken;
use App\Models\NotificationSendLog;
use App\Models\Setting;
use App\Services\Notifications\FcmSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendContestReminders extends Command
{
    protected $signature = 'notifications:contest-reminders
        {--dry-run : Do not send, only show what would be sent}';

    protected $description = 'Send contest start reminders via FCM (login-only)';

    public function handle(FcmSender $sender): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $now = now();
        $leadMinutes = (int) Setting::cachedGet('contest_reminder_lead_minutes', '30');
        $leadMinutes = max(5, min(180, $leadMinutes));
        $windowMinutes = 5;

        $soonFrom = $now->copy()->addMinutes($leadMinutes - $windowMinutes);
        $soonTo = $now->copy()->addMinutes($leadMinutes + $windowMinutes);

        $startedFrom = $now->copy()->subMinutes($windowMinutes);
        $startedTo = $now->copy()->addMinute();

        $candidates = Contest::query()
            ->whereNotNull('starts_at')
            ->whereIn('status', ['scheduled', 'live'])
            ->where(function ($q) use ($soonFrom, $soonTo, $startedFrom, $startedTo) {
                $q->whereBetween('starts_at', [$soonFrom, $soonTo])
                    ->orWhereBetween('starts_at', [$startedFrom, $startedTo]);
            })
            ->with('quiz')
            ->get();

        $sentAny = false;

        foreach ($candidates as $contest) {
            $contest->syncStatusFromSchedule();

            if (! $contest->starts_at) {
                continue;
            }

            // Stage: starting soon (≈30 minutes before).
            if ($contest->starts_at->between($soonFrom, $soonTo)) {
                $sentAny = $this->sendForContestStage(
                    sender: $sender,
                    contest: $contest,
                    stage: 'starting_soon',
                    title: 'Contest starts soon',
                    body: "{$contest->title} starts in ~{$leadMinutes} minutes",
                    dryRun: $dryRun,
                ) || $sentAny;
            }

            // Stage: started (near start time).
            if ($contest->starts_at->between($startedFrom, $startedTo)) {
                $sentAny = $this->sendForContestStage(
                    sender: $sender,
                    contest: $contest,
                    stage: 'started',
                    title: 'Contest is live',
                    body: "{$contest->title} is live now",
                    dryRun: $dryRun,
                ) || $sentAny;
            }
        }

        if (! $sentAny) {
            $this->info('No contest reminders to send right now.');
        }

        return self::SUCCESS;
    }

    private function sendForContestStage(
        FcmSender $sender,
        Contest $contest,
        string $stage,
        string $title,
        string $body,
        bool $dryRun
    ): bool {
        $uniqueKey = "contest_reminder:{$stage}:{$contest->id}";

        if (NotificationSendLog::query()->where('unique_key', $uniqueKey)->exists()) {
            return false;
        }

        $tokens = FcmToken::query()
            ->from('fcm_tokens as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->join('contest_participants as p', function ($j) use ($contest) {
                $j->on('p.user_id', '=', 'u.id')
                    ->where('p.contest_id', '=', $contest->id)
                    ->where('p.status', '=', 'joined');
            })
            ->whereNull('t.revoked_at')
            ->whereNull('u.blocked_at')
            ->where('u.is_guest', false)
            ->select('t.token')
            ->pluck('token')
            ->unique()
            ->values()
            ->all();

        if (count($tokens) === 0) {
            return false;
        }

        $payload = [
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => [
                'type' => 'contest_reminder',
                'stage' => $stage,
                'contest_id' => (string) $contest->id,
                'url' => route('student.contests.show', $contest),
            ],
        ];

        $this->info("[{$stage}] {$contest->title} → tokens: " . count($tokens));

        if ($dryRun) {
            $this->warn('Dry run: not sending.');
            return true;
        }

        // Idempotency record (unique constraint ensures only one wins).
        try {
            DB::transaction(function () use ($uniqueKey, $payload, $tokens, $stage) {
                NotificationSendLog::query()->create([
                    'type' => "contest_reminder_{$stage}",
                    'unique_key' => $uniqueKey,
                    'payload' => $payload,
                    'recipient_count' => count($tokens),
                    'sent_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            // Another runner likely inserted first.
            return false;
        }

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
            return true;
        }

        $this->info("Sent. Success: {$success}, Failure: {$failure}");
        return true;
    }
}

