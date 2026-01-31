<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertFcmTokenRequest;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(UpsertFcmTokenRequest $request)
    {
        $user = $request->user();

        $token = (string) $request->validated('token');
        $tokenHash = hash('sha256', $token);

        FcmToken::query()->updateOrCreate(
            ['token_hash' => $tokenHash],
            [
                'user_id' => $user->id,
                'token' => $token,
                'platform' => $request->validated('platform'),
                'device_id' => $request->validated('device_id'),
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'min:20'],
        ]);

        $tokenHash = hash('sha256', (string) $request->input('token'));

        FcmToken::query()
            ->where('user_id', $request->user()->id)
            ->where('token_hash', $tokenHash)
            ->update(['revoked_at' => now()]);

        return response()->json(['ok' => true]);
    }
}

