<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ContestParticipant;
use App\Models\FcmToken;
use App\Models\InAppNotification;
use App\Models\QuizAttempt;
use App\Services\Notifications\FcmSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutboundNotificationsController extends Controller
{
    public function create()
    {
        abort_unless(Auth::user()?->hasRole('creator'), 403);

        return view('creator.notifications.send');
    }

    public function send(Request $request, FcmSender $sender)
    {
        abort_unless(Auth::user()?->hasRole('creator'), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'url' => ['nullable', 'string', 'max:500'],
            'audience' => ['required', 'array', 'min:1'],
            'audience.*' => ['string', 'in:quiz_players,contest_participants,club_members'],
            'send_push' => ['nullable', 'in:0,1'],
        ]);

        $creatorId = Auth::id();

        $userIds = [];

        if (in_array('quiz_players', $data['audience'], true)) {
            $ids = QuizAttempt::query()
                ->from('quiz_attempts as a')
                ->join('quizzes as q', 'q.id', '=', 'a.quiz_id')
                ->where('q.user_id', $creatorId)
                ->where('a.status', 'submitted')
                ->select('a.user_id')
                ->distinct()
                ->pluck('user_id')
                ->all();
            $userIds = array_merge($userIds, $ids);
        }

        if (in_array('contest_participants', $data['audience'], true)) {
            $ids = ContestParticipant::query()
                ->from('contest_participants as p')
                ->join('contests as c', 'c.id', '=', 'p.contest_id')
                ->where('c.creator_user_id', $creatorId)
                ->select('p.user_id')
                ->distinct()
                ->pluck('user_id')
                ->all();
            $userIds = array_merge($userIds, $ids);
        }

        if (in_array('club_members', $data['audience'], true)) {
            $clubIds = Club::query()->where('owner_user_id', $creatorId)->pluck('id')->all();
            if (count($clubIds) > 0) {
                $ids = ClubMember::query()
                    ->whereIn('club_id', $clubIds)
                    ->select('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->all();
                $userIds = array_merge($userIds, $ids);
            }
        }

        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        $userIds = array_values(array_filter($userIds, fn ($v) => $v > 0 && (int)$v !== (int)$creatorId));

        if (count($userIds) === 0) {
            return back()->withErrors(['notify' => 'No users found for selected audience.']);
        }

        // Create in-app notifications
        $rows = [];
        $now = now();
        foreach ($userIds as $uid) {
            $rows[] = [
                'user_id' => $uid,
                'type' => 'creator_announcement',
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $data['url'] ?? null,
                'data_json' => json_encode(['creator_user_id' => $creatorId], JSON_UNESCAPED_SLASHES),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            InAppNotification::query()->insert($chunk);
        }

        // Optional push
        $sent = 0;
        $failed = 0;
        if (($data['send_push'] ?? '0') === '1') {
            $tokens = FcmToken::query()
                ->whereNull('revoked_at')
                ->whereIn('user_id', $userIds)
                ->pluck('token')
                ->unique()
                ->values()
                ->all();

            $payload = [
                'priority' => 'high',
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                ],
                'data' => array_filter([
                    'url' => $data['url'] ?? null,
                    'type' => 'creator_announcement',
                ], fn ($v) => $v !== null && $v !== ''),
            ];

            foreach (array_chunk($tokens, 500) as $chunk) {
                $res = $sender->sendToTokens($chunk, $payload);
                if (!($res['ok'] ?? false)) {
                    continue;
                }
                $sent += (int) ($res['success'] ?? 0);
                $failed += (int) ($res['failure'] ?? 0);
            }
        }

        $msg = 'In-app notification created for ' . count($userIds) . ' users.';
        if (($data['send_push'] ?? '0') === '1') {
            $msg .= " Push sent: {$sent}, failed: {$failed}.";
        }

        return back()->with('status', $msg);
    }
}

