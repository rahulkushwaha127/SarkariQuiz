<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row items-center justify-between gap-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex-shrink-0">{{ __('Orders') }}</h2>
            <form method="GET" action="{{ route('company.orders.index') }}" data-async-target="#orders-list" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search invoice') }}" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm min-w-[150px] max-w-[200px]">
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
        <div id="orders-list" data-async-list data-method="post" data-src="{{ route('company.orders.filter') }}">
            @include('orders._company_list_content', ['orders' => $orders])
        </div>
    </div>
</x-app-layout>

