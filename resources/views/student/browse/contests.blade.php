@extends('layouts.student')

@section('title', 'Public Contests')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Public contests</div>
        <div class="mt-1 text-sm text-stone-600">Browse public contests and leaderboards.</div>
    </div>

    @if(($contests ?? collect())->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-600 shadow-sm">No public contests yet.</div>
    @else
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            @foreach($contests as $contest)
                <a href="{{ route('public.contests.show', $contest) }}"
                   class="block border-b border-stone-200 px-4 py-3 last:border-b-0 hover:bg-stone-50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $contest->title }}</div>
                            <div class="mt-1 text-xs text-stone-500">
                                Status: {{ $contest->status }} · Host: {{ $contest->creator?->name ?? '—' }} · Participants: {{ $contest->participants_count }}
                            </div>
                            @if($contest->quiz)
                                <div class="mt-1 text-xs text-stone-500">Quiz: {{ $contest->quiz->title }}</div>
                            @endif
                        </div>
                        <div class="text-xs text-stone-500">Open</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-xs text-stone-500">
            {{ $contests->links() }}
        </div>
    @endif
</div>
@endsection
