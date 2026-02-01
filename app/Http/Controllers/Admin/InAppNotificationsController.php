<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InAppNotificationsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasAnyRole(['admin', 'super_admin']), 403);

        $items = InAppNotification::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(30);

        return view('admin.inbox.index', compact('items'));
    }

    public function markRead(Request $request, InAppNotification $notification)
    {
        abort_unless(Auth::user()?->hasAnyRole(['admin', 'super_admin']), 403);
        abort_unless((int) $notification->user_id === (int) Auth::id(), 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->url) {
            return redirect($notification->url);
        }

        return back()->with('status', 'Marked as read.');
    }

    public function markAllRead(Request $request)
    {
        abort_unless(Auth::user()?->hasAnyRole(['admin', 'super_admin']), 403);

        InAppNotification::query()
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', 'All marked as read.');
    }
}

