@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
@php
    $currentStreak = (int) ($streak?->current_streak ?? 0);
    $bestStreak = (int) ($streak?->best_streak ?? 0);
    $totalXp = (int) ($streak?->total_xp ?? 0);
    $level = (int) ($streak?->level ?? 1);
    $levelName = $streak ? $streak->levelName() : 'Beginner';
    $xpProgress = $streak ? $streak->xpProgress() : 0;
    $xpNext = $streak ? $streak->xpForNextLevel() : 100;
@endphp

<div class="space-y-6">
    {{-- Profile hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white/20 text-2xl font-bold backdrop-blur">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-bold tracking-tight truncate">{{ $user->name }}</h1>
                <div class="mt-1 flex items-center gap-2">
                    <span class="rounded-lg bg-white/20 px-2 py-0.5 text-xs font-semibold backdrop-blur">Lv {{ $level }}</span>
                    <span class="text-sm text-sky-100">{{ $levelName }}</span>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex justify-between text-[10px] font-medium text-sky-100">
                <span>{{ number_format($totalXp) }} XP</span>
                <span>{{ number_format($xpNext) }} XP</span>
            </div>
            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-white/25">
                <div class="h-full rounded-full bg-white transition-all" style="width: {{ $xpProgress }}%"></div>
            </div>
        </div>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm text-center">
            <div class="text-2xl font-bold tabular-nums text-amber-600">{{ $currentStreak }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Day streak</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm text-center">
            <div class="text-2xl font-bold tabular-nums text-stone-800">{{ $bestStreak }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Best streak</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm text-center">
            <div class="text-2xl font-bold tabular-nums text-stone-800">{{ (int) ($stats->unique_quizzes ?? 0) }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Quizzes played</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm text-center">
            <div class="text-2xl font-bold tabular-nums text-emerald-600">{{ $accuracy }}%</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Accuracy</div>
        </div>
    </div>

    {{-- Default language --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-bold text-stone-800">Default language</h2>
        <p class="mt-1 text-sm text-stone-500">Quizzes and practice content will be shown in this language when available.</p>
        <form method="post" action="{{ route('student.profile.update_language') }}" class="mt-4 space-y-3">
            @csrf
            @method('patch')
            <select name="preferred_language" class="student-select w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                @foreach($supportedLanguages ?? [] as $code => $label)
                    <option value="{{ $code }}" {{ old('preferred_language', $user->studentProfile?->preferred_language ?? 'en') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                Save language
            </button>
        </form>
    </div>

    {{-- Share --}}
    @php
        $profileUrl = route('student.profile');
        $shareText = "I'm Level {$level} ({$levelName}) with {$currentStreak}-day streak and {$accuracy}% accuracy on " . ($siteName ?? config('app.name', 'QuizWhiz')) . "!";
        $waUrl = 'https://wa.me/?text=' . urlencode($shareText . "\n" . $profileUrl);
        $tgUrl = 'https://t.me/share/url?url=' . urlencode($profileUrl) . '&text=' . urlencode($shareText);
    @endphp
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-bold text-stone-800">Share progress</h2>
        <div class="mt-3 flex flex-nowrap gap-2 overflow-x-auto pb-1">
            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
               class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#20bd5a] transition-colors">
                WhatsApp
            </a>
            <a href="{{ $tgUrl }}" target="_blank" rel="noopener"
               class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[#0088cc] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#0077b5] transition-colors">
                Telegram
            </a>
            <button type="button" id="copy-profile-btn"
                    class="shrink-0 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition-colors"
                    data-copy-text="{{ $shareText . "\n" . $profileUrl }}">
                Copy link
            </button>
        </div>
    </div>

    <div class="pt-2">
        <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1 text-sm font-medium text-stone-600 hover:text-sky-600 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to dashboard
        </a>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var btn = document.getElementById('copy-profile-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            var text = this.getAttribute('data-copy-text');
            navigator.clipboard.writeText(text).then(function() {
                btn.textContent = 'Copied!';
                setTimeout(function() { btn.textContent = 'Copy link'; }, 2000);
            });
        });
    }
})();
</script>
@endpush
@endsection
