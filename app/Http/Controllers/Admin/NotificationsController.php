<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\InAppNotification;
use App\Models\User;
use App\Services\Notifications\FcmSender;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $tokensCount = FcmToken::query()->whereNull('revoked_at')->count();

        return view('admin.notifications.index', compact('tokensCount'));
    }

    public function send(Request $request, FcmSender $sender)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'url' => ['nullable', 'string', 'max:500'],
        ]);

        // Recipients: all real users (exclude guest accounts)
        $recipientIds = User::query()
            ->where('is_guest', false)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['student', 'creator', 'super_admin']))
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        // Create in-app notifications for all recipients (even if push fails / user disabled push).
        $now = now();
        $rows = [];
        foreach ($recipientIds as $uid) {
            $rows[] = [
                'user_id' => $uid,
                'type' => 'admin_announcement',
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $data['url'] ?? null,
                'data_json' => json_encode(['source' => 'admin'], JSON_UNESCAPED_SLASHES),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            InAppNotification::query()->insert($chunk);
        }

        // Push: only to users who enabled it (have active tokens).
        $tokens = FcmToken::query()
            ->whereNull('revoked_at')
            ->whereIn('user_id', $recipientIds)
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
                'type' => 'admin_announcement',
            ], fn ($v) => $v !== null && $v !== ''),
        ];

        // FCM legacy supports max 500 registration_ids per request.
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
            return back()->withErrors(['fcm' => implode(' | ', array_unique($errors))]);
        }

        return back()->with('status', "Announcement sent. Success: {$success}, Failure: {$failure}");
    }
}

