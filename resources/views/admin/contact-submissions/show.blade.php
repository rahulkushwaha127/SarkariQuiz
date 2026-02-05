@extends('layouts.admin')

@section('title', 'Contact message')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('admin.contact-submissions.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">← Back to list</a>
        <div class="flex gap-2">
            @if (!$contactSubmission->read_at)
                <form method="POST" action="{{ route('admin.contact-submissions.read', $contactSubmission) }}">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Mark as read</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.contact-submissions.destroy', $contactSubmission) }}" onsubmit="return confirm('Delete this message?');">
                @csrf
                @method('DELETE')
                <button class="rounded-xl border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Name</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $contactSubmission->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</dt>
                <dd class="mt-1 text-sm">
                    <a href="mailto:{{ $contactSubmission->email }}" class="text-violet-600 hover:underline">{{ $contactSubmission->email }}</a>
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $contactSubmission->subject }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Message</dt>
                <dd class="mt-2 whitespace-pre-wrap rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-700">{{ $contactSubmission->message }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Submitted</dt>
                <dd class="mt-1 text-sm text-slate-600">{{ $contactSubmission->created_at->format('d M Y, H:i') }}</dd>
            </div>
            @if ($contactSubmission->read_at)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Read at</dt>
                <dd class="mt-1 text-sm text-slate-600">{{ $contactSubmission->read_at->format('d M Y, H:i') }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection
