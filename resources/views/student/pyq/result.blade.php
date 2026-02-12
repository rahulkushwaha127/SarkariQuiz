@extends('layouts.student')

@section('title', 'PYQ Result')

@section('content')
    <div class="space-y-4">
        {{-- XP earned --}}
        @php
            $xpData = session()->pull('xp_result', []);
            $xpEarned = $xpData['xp_earned'] ?? (10 + ((int) $attempt->correct_count * 2));
            $leveledUp = $xpData['leveled_up'] ?? false;
            $newLevel = $xpData['new_level'] ?? 1;
            $newLevelName = \App\Models\DailyStreak::LEVEL_NAMES[$newLevel] ?? 'Beginner';
        @endphp
        @if($leveledUp)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center">
                <div class="text-xs font-semibold text-amber-700">LEVEL UP!</div>
                <div class="mt-1 text-2xl font-bold text-amber-800">Level {{ $newLevel }} — {{ $newLevelName }}</div>
                <div class="mt-1 text-sm text-amber-700">+{{ $xpEarned }} XP</div>
            </div>
        @else
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-3 text-center">
                <div class="text-xs font-semibold text-indigo-600">XP earned</div>
                <div class="text-2xl font-bold text-indigo-800">+{{ $xpEarned }} XP</div>
            </div>
        @endif

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">PYQ Result</div>
            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Score</div>
                    <div class="mt-1 text-lg font-bold text-stone-800">{{ (int) $attempt->score }}</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Time</div>
                    <div class="mt-1 text-lg font-bold text-stone-800">{{ (int) $attempt->time_taken_seconds }}s</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Correct</div>
                    <div class="mt-1 text-lg font-bold text-emerald-600">{{ (int) $attempt->correct_count }}</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Wrong</div>
                    <div class="mt-1 text-lg font-bold text-rose-600">{{ (int) $attempt->wrong_count }}</div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @if($frontendMenu['pyq'] ?? true)
                <a href="{{ route('pyq.index') }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    PYQ again
                </a>
                @endif
                @if($frontendMenu['practice'] ?? true)
                <a href="{{ route('practice') }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Practice
                </a>
                @endif
                <a href="{{ route('public.home') }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Home
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">Review</div>

            @foreach($questions as $q)
                @php
                    $slot = $slots->get($q->id);
                    $selectedId = $slot?->pyq_answer_id;
                    $correctId = $q->answers->firstWhere('is_correct', true)?->id;
                @endphp

                <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                    <div class="text-sm font-semibold text-stone-800">
                        #{{ $loop->iteration }}. {!! nl2br(e($q->prompt)) !!}
                    </div>
                    @if($q->paper || $q->year)
                        <div class="mt-1 text-xs text-stone-500">
                            {{ trim(($q->paper ? $q->paper : '') . ($q->year ? (' · ' . $q->year) : '')) }}
                        </div>
                    @endif

                    <div class="mt-2 space-y-2 text-sm">
                        @foreach($q->answers as $ans)
                            @php
                                $isSelected = $selectedId && (int)$selectedId === (int)$ans->id;
                                $isCorrect = $correctId && (int)$correctId === (int)$ans->id;
                                $rowClass = 'rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-stone-800';
                                if ($isCorrect) $rowClass = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-emerald-800';
                                elseif ($isSelected) $rowClass = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-rose-800';
                            @endphp
                            <div class="{{ $rowClass }}">
                                {{ $ans->title }}
                                @if($isCorrect)
                                    <span class="ml-2 text-xs font-semibold text-emerald-600">(correct)</span>
                                @elseif($isSelected)
                                    <span class="ml-2 text-xs font-semibold text-rose-600">(your choice)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($q->explanation)
                        <div class="mt-3 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-700">
                            <div class="text-xs font-semibold text-stone-600">Explanation</div>
                            <div class="mt-1">{!! nl2br(e($q->explanation)) !!}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
