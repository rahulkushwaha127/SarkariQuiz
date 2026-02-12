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

<div class="space-y-4">
    {{-- Profile card (light theme) --}}
    <div id="profile-card" class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        {{-- Name & Level --}}
        <div class="flex items-center gap-3">
            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-sky-100 text-xl font-bold text-sky-700">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="truncate text-lg font-bold text-stone-800">{{ $user->name }}</div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-lg bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-700">Lv {{ $level }}</span>
                    <span class="text-xs text-stone-500">{{ $levelName }}</span>
                </div>
            </div>
        </div>

        {{-- XP bar --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-[10px] text-stone-500">
                <span>{{ number_format($totalXp) }} XP</span>
                <span>{{ number_format($xpNext) }} XP</span>
            </div>
            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-stone-200">
                <div class="h-full rounded-full bg-sky-500 transition-all" style="width: {{ $xpProgress }}%"></div>
            </div>
        </div>

        {{-- Stats grid --}}
        <div class="mt-4 grid grid-cols-2 gap-2">
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 text-center">
                <div class="text-xl font-bold text-amber-600">{{ $currentStreak }}</div>
                <div class="text-[10px] text-stone-500">Day streak</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 text-center">
                <div class="text-xl font-bold text-stone-800">{{ $bestStreak }}</div>
                <div class="text-[10px] text-stone-500">Best streak</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 text-center">
                <div class="text-xl font-bold text-stone-800">{{ (int) ($stats->unique_quizzes ?? 0) }}</div>
                <div class="text-[10px] text-stone-500">Quizzes played</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 text-center">
                <div class="text-xl font-bold text-emerald-600">{{ $accuracy }}%</div>
                <div class="text-[10px] text-stone-500">Accuracy</div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="mt-3 text-center text-[10px] text-stone-400">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
    </div>

    {{-- Default language --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-bold text-stone-800">Default language</h3>
        <p class="mt-1 text-xs text-stone-500">Quizzes and practice content will be shown in this language when available.</p>
        <form method="post" action="{{ route('student.profile.update_language') }}" class="mt-3">
            @csrf
            @method('patch')
            <select name="preferred_language" class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                @foreach($supportedLanguages ?? [] as $code => $label)
                    <option value="{{ $code }}" {{ old('preferred_language', $user->studentProfile?->preferred_language ?? 'en') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="mt-3 w-full rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500">
                Save language
            </button>
        </form>
    </div>

    {{-- Share buttons --}}
    @php
        $profileUrl = route('student.profile');
        $shareText = "I'm Level {$level} ({$levelName}) with {$currentStreak}-day streak and {$accuracy}% accuracy on " . ($siteName ?? config('app.name', 'QuizWhiz')) . "!";
        $waUrl = 'https://wa.me/?text=' . urlencode($shareText . "\n" . $profileUrl);
        $tgUrl = 'https://t.me/share/url?url=' . urlencode($profileUrl) . '&text=' . urlencode($shareText);
    @endphp
    <div class="flex flex-wrap gap-2">
        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
           class="flex-1 rounded-xl bg-green-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-green-500">
            Share on WhatsApp
        </a>
        <a href="{{ $tgUrl }}" target="_blank" rel="noopener"
           class="flex-1 rounded-xl bg-sky-500 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-sky-400">
            Share on Telegram
        </a>
    </div>
    <button type="button" id="copy-profile-btn"
            class="w-full rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50"
            data-copy-text="{{ $shareText . "\n" . $profileUrl }}">
        Copy to clipboard
    </button>

    <div class="pt-2">
        <a href="{{ route('public.home') }}" class="text-sm font-medium text-stone-600 hover:text-sky-600">← Back to dashboard</a>
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
                setTimeout(function() { btn.textContent = 'Copy to clipboard'; }, 2000);
            });
        });
    }
})();
</script>
@endpush
@endsection
