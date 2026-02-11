<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use Illuminate\Http\Request;

class ContestsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $allowedStatuses = ['draft', 'scheduled', 'live', 'ended', 'cancelled', ''];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = '';
        }

        $public = $request->string('public')->toString(); // 1|0|''
        if (!in_array($public, ['1', '0', ''], true)) {
            $public = '';
        }

        $contests = Contest::query()
            ->with(['creator:id,name,email,username', 'quiz:id,title,unique_code'])
            ->withCount('participants')
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($public !== '', fn ($q) => $q->where('is_public_listed', $public === '1'))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // keep schedule status up to date for the list
        foreach ($contests as $c) {
            $c->syncStatusFromSchedule();
        }

        return view('admin.contests.index', compact('contests', 'status', 'public'));
    }

    public function togglePublic(Request $request, Contest $contest)
    {
        $contest->update([
            'is_public_listed' => ! (bool) $contest->is_public_listed,
        ]);

        return back()->with('status', $contest->is_public_listed ? 'Contest listed publicly.' : 'Contest hidden from public listing.');
    }

    public function toggleActive(Request $request, Contest $contest)
    {
        $contest->update(['is_active' => ! (bool) $contest->is_active]);
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['active' => (bool) $contest->is_active]);
        }
        return back()->with('status', $contest->is_active ? 'Contest visible.' : 'Contest hidden.');
    }

    public function cancel(Request $request, Contest $contest)
    {
        if ($contest->status === 'ended') {
            return back()->withErrors(['contest' => 'Ended contest cannot be cancelled.']);
        }

        $contest->update([
            'status' => 'cancelled',
            'is_public_listed' => false,
        ]);

        return back()->with('status', 'Contest cancelled.');
    }
}

