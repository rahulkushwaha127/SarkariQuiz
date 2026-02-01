@extends('layouts.student')

@section('title', 'Public Contests')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Public contests</div>
        <div class="mt-1 text-sm text-slate-300">Browse public contests and leaderboards.</div>
    </div>

    @if(($contests ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No public contests yet.</div>
    @else
        <div class="border border-white/10 bg-white/5">
            @foreach($contests as $contest)
                <a href="{{ route('public.contests.show', $contest) }}"
                   class="block border-b border-white/10 px-4 py-3 last:border-b-0 hover:bg-white/5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $contest->title }}</div>
                            <div class="mt-1 text-xs text-slate-300">
                                Status: {{ $contest->status }} · Host: {{ $contest->creator?->name ?? '—' }} · Participants: {{ $contest->participants_count }}
                            </div>
                            @if($contest->quiz)
                                <div class="mt-1 text-xs text-slate-400">Quiz: {{ $contest->quiz->title }}</div>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400">Open</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-xs text-slate-400">
            {{ $contests->links() }}
        </div>
    @endif
</div>
@endsection


