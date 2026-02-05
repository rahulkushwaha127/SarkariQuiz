<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionsController extends Controller
{
    public function index(Request $request)
    {
        $submissions = ContactSubmission::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.contact-submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $contactSubmission)
    {
        if (!$contactSubmission->read_at) {
            $contactSubmission->update(['read_at' => now()]);
        }

        return view('admin.contact-submissions.show', compact('contactSubmission'));
    }

    public function markRead(ContactSubmission $contactSubmission)
    {
        if (!$contactSubmission->read_at) {
            $contactSubmission->update(['read_at' => now()]);
        }

        return back()->with('status', 'Marked as read.');
    }

    public function destroy(ContactSubmission $contactSubmission)
    {
        $contactSubmission->delete();

        return back()->with('status', 'Submission deleted.');
    }
}
