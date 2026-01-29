<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('plans.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Orders for :plan', ['plan' => $plan->name]) }}</h2>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-slate-400 mt-1">{{ __('Transaction history for this plan') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div id="orders-list" data-async-list data-method="post" data-src="{{ route('plans.orders.filter', $plan) }}">
            <div class="py-10 flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-app-layout>

