@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Contact messages</h1>
        <p class="mt-1 text-sm text-slate-600">Messages submitted via the public contact form.</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $s)
                        <tr class="hover:bg-slate-50 {{ !$s->read_at ? 'bg-violet-50/50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $s->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <a href="mailto:{{ $s->email }}" class="text-violet-600 hover:underline">{{ $s->email }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ Str::limit($s->subject, 40) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $s->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if ($s->read_at)
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">Read</span>
                                @else
                                    <span class="rounded-full bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-800">New</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.contact-submissions.show', $s) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        View
                                    </a>
                                    <form method="POST" action="{{ route('admin.contact-submissions.destroy', $s) }}" class="inline" onsubmit="return confirm('Delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No contact messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($submissions->hasPages())
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
@endsection
