@extends('layouts.student')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Leaderboard</div>
        <div class="mt-1 text-sm text-slate-300">{{ $label }}</div>
    </div>

    <div class="border border-white/10 bg-white/5">
        <div class="flex items-center gap-2 px-4 py-3">
            <a href="{{ route('student.leaderboard', ['period' => 'daily']) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'daily' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                Daily
            </a>
            <a href="{{ route('student.leaderboard', ['period' => 'weekly']) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'weekly' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                Weekly
            </a>
            <a href="{{ route('student.leaderboard', ['period' => 'all']) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'all' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                All time
            </a>
        </div>
    </div>

    <div class="border border-white/10 bg-white/5">
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
                            Attempts: {{ (int) $row->attempts }}
                        </div>
                    </div>
                    <div class="shrink-0 text-sm font-bold text-white">
                        {{ (int) $row->total_score }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

