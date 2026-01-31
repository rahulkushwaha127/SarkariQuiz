<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestWhitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContestWhitelistController extends Controller
{
    public function index(Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $entries = ContestWhitelist::query()
            ->where('contest_id', $contest->id)
            ->orderByDesc('id')
            ->paginate(50);

        return view('creator.contests.whitelist', compact('contest', 'entries'));
    }

    public function store(Request $request, Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $data = $request->validate([
            'emails' => ['required', 'string', 'max:5000'],
        ]);

        $emails = preg_split('/[\s,;]+/', $data['emails']) ?: [];
        $emails = array_values(array_unique(array_filter(array_map(fn ($e) => strtolower(trim($e)), $emails))));

        $added = 0;
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                ContestWhitelist::query()->firstOrCreate([
                    'contest_id' => $contest->id,
                    'email' => $email,
                ]);
                $added++;
            } catch (\Throwable $e) {
                // ignore duplicates/race
            }
        }

        return redirect()
            ->route('creator.contests.whitelist.index', $contest)
            ->with('status', $added > 0 ? "Added {$added} email(s)." : 'No valid emails to add.');
    }

    public function destroy(Contest $contest, ContestWhitelist $entry)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);
        abort_unless((int)$entry->contest_id === (int)$contest->id, 404);

        $entry->delete();

        return redirect()
            ->route('creator.contests.whitelist.index', $contest)
            ->with('status', 'Removed.');
    }
}

