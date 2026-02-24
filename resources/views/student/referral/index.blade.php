@extends('layouts.student')

@section('title', 'Refer & earn')

@section('content')
@php
    $shareMessage = 'Join me on ' . config('app.name', 'QuizWhiz') . ' — use my link to sign up and start practicing!';
    $shareText = $shareMessage . ' ' . $referralLink;
    $encodedText = rawurlencode($shareText);
    $encodedUrl = rawurlencode($referralLink);
    $encodedMessage = rawurlencode($shareMessage . ' ' . $referralLink);
@endphp
<div class="space-y-6">
    {{-- Hero header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-6 text-white shadow-lg">
        <div class="relative z-10">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">Refer & earn</h1>
            <p class="mt-1.5 text-sm text-sky-100">
                When <strong class="text-white">{{ $mRequired }}</strong> friends subscribe, you get <strong class="text-white">1 month free</strong> — once per account.
            </p>
        </div>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-4 -left-4 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    {{-- Referral link + Share --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <label class="block text-sm font-semibold text-stone-700">Your referral link</label>
        <div class="mt-2 flex gap-2">
            <input type="text" readonly value="{{ $referralLink }}" id="referral-link-input"
                   class="min-w-0 flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-800 font-mono focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
            <button type="button" id="copy-referral-btn"
                    class="flex shrink-0 items-center gap-2 rounded-xl bg-stone-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-stone-700 transition-colors">
                <svg class="h-4 w-4 copy-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m2 4a2 2 0 01-2 2h-2m-4 0H8m0 0v4"/>
                </svg>
                <span class="copy-label">Copy</span>
            </button>
        </div>

        {{-- Share via social --}}
        <p class="mt-4 text-xs font-medium text-stone-500">Share via</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <a href="https://wa.me/?text={{ $encodedMessage }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#20bd5a] transition-colors"
               aria-label="Share on WhatsApp">
                {{-- WhatsApp icon --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
            <a href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ rawurlencode($shareMessage) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0088cc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0077b5] transition-colors"
               aria-label="Share on Telegram">
                {{-- Telegram icon --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                Telegram
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($shareMessage) }}&url={{ $encodedUrl }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#000000] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1a1a1a] transition-colors"
               aria-label="Share on X (Twitter)">
                {{-- X (Twitter) icon --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                X
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-stone-800">{{ $referredCount }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Friends referred</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-stone-800">{{ $convertedCount }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Subscribed</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-sky-600">{{ $mRequired }}</div>
            <div class="mt-0.5 text-xs font-medium text-stone-500">Required for reward</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm flex flex-col justify-center">
            @if($hasReceivedReward)
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Reward earned
                </span>
            @else
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Not yet
                </span>
            @endif
            <div class="mt-1 text-xs font-medium text-stone-500">Status</div>
        </div>
    </div>

    {{-- CTA / Progress message --}}
    @if(!$hasReceivedReward && $convertedCount < $mRequired)
        <div class="rounded-2xl border border-sky-100 bg-sky-50/80 p-4">
            <p class="text-sm text-stone-700">
                Share your link. When <strong class="text-sky-700">{{ $mRequired - $convertedCount }}</strong> more friend(s) sign up and subscribe, you'll get <strong>1 month free</strong>.
            </p>
        </div>
    @elseif($hasReceivedReward)
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 p-4">
            <p class="text-sm text-stone-700">
                You've earned your reward. Thanks for spreading the word!
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    var btn = document.getElementById('copy-referral-btn');
    var input = document.getElementById('referral-link-input');
    if (btn && input) {
        btn.addEventListener('click', function() {
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(function() {
                var label = btn.querySelector('.copy-label');
                if (label) label.textContent = 'Copied!';
                setTimeout(function() {
                    if (label) label.textContent = 'Copy';
                }, 2000);
            });
        });
    }
})();
</script>
@endpush
@endsection
