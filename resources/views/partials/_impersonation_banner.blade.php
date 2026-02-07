@if(app('impersonate')->isImpersonating())
<div class="fixed inset-x-0 top-0 z-[100] flex items-center justify-center gap-4 bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-md">
    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>
    <span>You are impersonating <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</span>
    <a href="{{ route('impersonate.leave') }}"
       class="inline-flex items-center gap-1 rounded-lg bg-white/20 px-3 py-1 text-xs font-bold text-white hover:bg-white/30">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
        </svg>
        Stop impersonating
    </a>
</div>
@endif
