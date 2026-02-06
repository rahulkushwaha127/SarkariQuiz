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
    {{-- Profile card --}}
    <div id="profile-card" class="border border-white/10 bg-gradient-to-br from-indigo-600/20 via-slate-900/80 to-purple-600/20 p-5">
        {{-- Name & Level --}}
        <div class="flex items-center gap-3">
            <div class="grid h-14 w-14 shrink-0 place-items-center bg-indigo-500/30 text-xl font-bold text-white">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="truncate text-lg font-bold text-white">{{ $user->name }}</div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center bg-indigo-500/30 px-2 py-0.5 text-xs font-semibold text-indigo-200">Lv {{ $level }}</span>
                    <span class="text-xs text-slate-300">{{ $levelName }}</span>
                </div>
            </div>
        </div>

        {{-- XP bar --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-[10px] text-slate-400">
                <span>{{ number_format($totalXp) }} XP</span>
                <span>{{ number_format($xpNext) }} XP</span>
            </div>
            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-white/10">
                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $xpProgress }}%"></div>
            </div>
        </div>

        {{-- Stats grid --}}
        <div class="mt-4 grid grid-cols-2 gap-2">
            <div class="border border-white/10 bg-black/20 p-3 text-center">
                <div class="text-xl font-bold text-orange-300">{{ $currentStreak }}</div>
                <div class="text-[10px] text-slate-400">Day streak</div>
            </div>
            <div class="border border-white/10 bg-black/20 p-3 text-center">
                <div class="text-xl font-bold text-white">{{ $bestStreak }}</div>
                <div class="text-[10px] text-slate-400">Best streak</div>
            </div>
            <div class="border border-white/10 bg-black/20 p-3 text-center">
                <div class="text-xl font-bold text-white">{{ (int) ($stats->unique_quizzes ?? 0) }}</div>
                <div class="text-[10px] text-slate-400">Quizzes played</div>
            </div>
            <div class="border border-white/10 bg-black/20 p-3 text-center">
                <div class="text-xl font-bold text-emerald-300">{{ $accuracy }}%</div>
                <div class="text-[10px] text-slate-400">Accuracy</div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="mt-3 text-center text-[10px] text-slate-500">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
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
           class="flex-1 bg-green-600/80 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-green-600">
            Share on WhatsApp
        </a>
        <a href="{{ $tgUrl }}" target="_blank" rel="noopener"
           class="flex-1 bg-blue-500/80 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-500">
            Share on Telegram
        </a>
    </div>
    <button type="button" id="copy-profile-btn"
            class="w-full bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15"
            data-copy-text="{{ $shareText . "\n" . $profileUrl }}">
        Copy to clipboard
    </button>

    <div class="pt-2">
        <a href="{{ route('public.home') }}" class="text-sm text-indigo-300 hover:text-indigo-200">← Back to dashboard</a>
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
