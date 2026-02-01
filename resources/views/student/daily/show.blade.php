@extends('layouts.student')

@section('title', 'Daily Challenge')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Daily Challenge</div>
            <div class="mt-1 text-sm text-slate-300">Date: {{ $today }}</div>
        </div>

        @if(!$daily?->quiz)
            <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
                No daily challenge yet.
            </div>
        @else
            <div class="border border-white/10 bg-white/5 p-4">
                <div class="text-base font-semibold text-white">{{ $daily->quiz->title }}</div>
                <div class="mt-1 text-sm text-slate-300">By: {{ $daily->quiz->user?->name ?? '—' }}</div>

                <a href="{{ route('student.quizzes.play', $daily->quiz) }}"
                   class="mt-3 inline-block bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                    PLAY NOW
                </a>

                @if($myRank)
                    <div class="mt-3 text-sm text-slate-200">
                        Your rank (top 50): <span class="font-semibold text-white">#{{ $myRank }}</span>
                    </div>
                @endif
            </div>

            <div class="border border-white/10 bg-white/5">
                <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">Leaderboard (today)</div>

                @if(($rows ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-slate-300">No attempts yet.</div>
                @else
                    @foreach($rows as $row)
                        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 text-sm font-extrabold text-white/90">#{{ $loop->iteration }}</div>
                                    <div class="truncate text-sm font-semibold text-white">{{ $row->user_name }}</div>
                                </div>
                                <div class="mt-1 pl-12 text-xs text-slate-400">
                                    Best time: {{ (int) $row->best_time }}s · Attempts: {{ (int) $row->attempts }}
                                </div>
                            </div>
                            <div class="shrink-0 text-sm font-bold text-white">
                                {{ (int) $row->best_score }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
@endsection

