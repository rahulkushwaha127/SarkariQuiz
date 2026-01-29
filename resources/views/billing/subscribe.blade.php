<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Choose a plan') }}</h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $p)
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $p->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-slate-400">{{ strtoupper($p->currency) }} {{ number_format($p->unit_amount/100, 2) }} / {{ $p->interval }}</p>
                </div>
                @php(
                    $isActive = ($subscription?->tx_status === 'succeeded' && $subscription?->plan_code === $p->code)
                )
                @php(
                    $isTrial = ($trial?->status === 'trialing' && $trial?->plan_code === $p->code)
                )
                @php(
                    $expiresAt = null
                )
                @php(
                    $expiresAt = $isTrial
                        ? ($trial && $trial->trial_ends_at ? $trial->trial_ends_at : null)
                        : ($isActive && $subscription?->occurred_at
                            ? ($p->interval === 'year' ? $subscription->occurred_at->copy()->addYear() : $subscription->occurred_at->copy()->addMonth())
                            : null)
                )
                @php(
                    $daysLeft = null
                )
                @php(
                    $daysLeft = $expiresAt
                        ? (function($exp){
                            $expC = $exp instanceof \Carbon\Carbon ? $exp->copy() : \Carbon\Carbon::parse($exp);
                            return now()->startOfDay()->diffInDays($expC->startOfDay(), false);
                        })($expiresAt)
                        : null
                )
                @if($expiresAt)
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                        {{ $daysLeft !== null && $daysLeft > 0 ? __('Expiring in :days days', ['days' => $daysLeft]) : __('Expiring today') }}
                    </span>
                @endif
            </div>
            @if($p->description)
                <p class="mt-2 text-sm text-gray-700 dark:text-slate-300">{{ $p->description }}</p>
            @endif
            @if($p->users_count || $p->teams_count || $p->roles_count)
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @if($p->users_count)
                        <span class="px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ __('Up to :n users', ['n' => $p->users_count]) }}</span>
                    @endif
                    @if($p->teams_count)
                        <span class="px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ __('Up to :n teams', ['n' => $p->teams_count]) }}</span>
                    @endif
                    @if($p->roles_count)
                        <span class="px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ __('Up to :n roles', ['n' => $p->roles_count]) }}</span>
                    @endif
                </div>
            @endif
            <div class="mt-4 flex flex-col md:flex-row items-stretch md:items-center gap-2">
                @php($hasPendingManual = in_array($p->code, $pendingManualRequests ?? []))
                
                @if($hasPendingManual)
                    <button class="px-4 py-2 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 cursor-not-allowed" disabled>
                        {{ __('Manual Requested') }}
                    </button>
                @elseif($isTrial)
                    <button class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-800 text-gray-600 dark:text-slate-400 cursor-not-allowed" disabled>
                        {{ __('On trial till :date', ['date' => optional($trial->trial_ends_at)->format('Y-m-d')]) }}
                    </button>
                @elseif($isActive)
                    <button class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-800 text-gray-600 dark:text-slate-400 cursor-not-allowed" disabled>{{ __('Subscribed') }}</button>
                @else
                    <a href="{{ route('billing.index', ['plan' => $p]) }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-center md:text-left">{{ __('Subscribe') }}</a>
                    @if($p->trial_days && empty($hasTrialTaken))
                    <form method="POST" action="{{ route('billing.subscribe') }}" data-confirm="{{ __('Start trial for :plan?', ['plan' => $p->name]) }}" data-confirm-ok="{{ __('Start trial') }}" data-confirm-color="green" class="w-full md:w-auto">
                        @csrf
                        <input type="hidden" name="plan_code" value="{{ $p->code }}">
                        <button class="w-full md:w-auto px-4 py-2 rounded-xl border">{{ __('Start trial (:days days)', ['days' => $p->trial_days]) }}</button>
                    </form>
                    @elseif($p->trial_days && !empty($hasTrialTaken))
                        <span class="text-xs text-gray-500 dark:text-slate-400">{{ __('Trial already used') }}</span>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>


