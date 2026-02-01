<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        if (!in_array($status, ['active', 'disabled', ''], true)) {
            $status = '';
        }

        $clubs = Club::query()
            ->with(['owner:id,name,email'])
            ->withCount('members')
            ->withCount(['sessions as active_sessions_count' => fn ($q) => $q->where('status', 'active')])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.clubs.index', compact('clubs', 'status'));
    }

    public function toggleStatus(Request $request, Club $club)
    {
        $club->update([
            'status' => $club->status === 'active' ? 'disabled' : 'active',
        ]);

        return back()->with('status', $club->status === 'active' ? 'Club enabled.' : 'Club disabled.');
    }
}

