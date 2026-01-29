<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row items-center justify-between gap-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex-shrink-0">{{ __('Orders') }}</h2>
            <form method="GET" action="{{ route('orders.index') }}" data-async-target="#orders-list" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search invoice, company') }}" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm min-w-[150px] max-w-[200px]">
                <select name="company_id" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                    <option value="">{{ __('All Companies') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                <select name="plan_code" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                    <option value="">{{ __('All Plans') }}</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->code }}" {{ request('plan_code') == $plan->code ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>{{ __('Succeeded') }}</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                </select>
                <select name="provider" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                    <option value="">{{ __('All Providers') }}</option>
                    <option value="manual" {{ request('provider') == 'manual' ? 'selected' : '' }}>{{ __('Manual') }}</option>
                    <option value="stripe" {{ request('provider') == 'stripe' ? 'selected' : '' }}>{{ __('Stripe') }}</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('From') }}" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="{{ __('To') }}" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
            </form>
        </div>
    </x-slot>

    <div class="card">
        <div id="orders-list" data-async-list data-method="post" data-src="{{ route('orders.filter') }}">
            <div class="py-10 flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-app-layout>

