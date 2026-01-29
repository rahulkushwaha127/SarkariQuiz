<div class="p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Review Manual Payment Request') }}</h3>
    </div>
    
    <div class="space-y-4">
        <!-- Company Details -->
        <div>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">{{ __('Company Details') }}</h4>
            <div class="p-3 rounded-xl border bg-white dark:bg-slate-900 dark:border-slate-800">
                <p class="text-sm text-gray-900 dark:text-white"><strong>{{ __('Name') }}:</strong> {{ $billing->company->name ?? __('Unknown') }}</p>
                @if($billing->company->website)
                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1"><strong>{{ __('Website') }}:</strong> <a href="{{ $billing->company->website }}" target="_blank" class="text-primary-600 hover:underline">{{ $billing->company->website }}</a></p>
                @endif
                @if($billing->company->description)
                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1"><strong>{{ __('Description') }}:</strong> {{ $billing->company->description }}</p>
                @endif
            </div>
        </div>

        <!-- Plan Details -->
        <div>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">{{ __('Plan Details') }}</h4>
            <div class="p-3 rounded-xl border bg-white dark:bg-slate-900 dark:border-slate-800">
                @if($plan)
                    <p class="text-sm text-gray-900 dark:text-white"><strong>{{ __('Plan') }}:</strong> {{ $plan->name }}</p>
                    @if($plan->description)
                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">{{ $plan->description }}</p>
                    @endif
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @if($plan->trial_days)
                            <div class="p-2 rounded-lg border bg-gray-50 dark:bg-slate-800">
                                <p class="text-xs text-gray-500 dark:text-slate-500">{{ __('Trial') }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->trial_days }} {{ __('days') }}</p>
                            </div>
                        @endif
                        @if($plan->users_count)
                            <div class="p-2 rounded-lg border bg-gray-50 dark:bg-slate-800">
                                <p class="text-xs text-gray-500 dark:text-slate-500">{{ __('Users') }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->users_count }}</p>
                            </div>
                        @endif
                        @if($plan->teams_count)
                            <div class="p-2 rounded-lg border bg-gray-50 dark:bg-slate-800">
                                <p class="text-xs text-gray-500 dark:text-slate-500">{{ __('Teams') }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->teams_count }}</p>
                            </div>
                        @endif
                        @if($plan->roles_count)
                            <div class="p-2 rounded-lg border bg-gray-50 dark:bg-slate-800">
                                <p class="text-xs text-gray-500 dark:text-slate-500">{{ __('Roles') }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->roles_count }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-slate-400">{{ $billing->plan_code ?? __('Unknown') }}</p>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">{{ __('Payment Details') }}</h4>
            <div class="p-3 rounded-xl border bg-white dark:bg-slate-900 dark:border-slate-800">
                <p class="text-sm text-gray-900 dark:text-white"><strong>{{ __('Invoice') }}:</strong> {{ $billing->invoice_number ?? '—' }}</p>
                <p class="text-sm text-gray-900 dark:text-white mt-1"><strong>{{ __('Amount') }}:</strong> {{ strtoupper($billing->currency ?? 'USD') }} {{ number_format(($billing->amount ?? 0) / 100, 2) }}</p>
                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1"><strong>{{ __('Date') }}:</strong> {{ $billing->occurred_at ? $billing->occurred_at->format('Y-m-d H:i') : '—' }}</p>
                @if($billing->notes)
                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1"><strong>{{ __('Notes') }}:</strong> {{ $billing->notes }}</p>
                @endif
            </div>
        </div>

        <!-- Receipt -->
        @if($billing->ref_url)
        <div>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">{{ __('Receipt') }}</h4>
            <div class="p-3 rounded-xl border bg-white dark:bg-slate-900 dark:border-slate-800">
                <a href="{{ $billing->ref_url }}" target="_blank" class="inline-flex items-center gap-2 text-primary-600 hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('View Receipt') }}
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end gap-3">
        <button type="button" 
                class="px-4 py-2 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700"
                onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ajax-modal' }))">
            {{ __('Cancel') }}
        </button>
        <form method="POST" action="{{ route('admin.manual_requests.reject', $billing) }}" class="inline" data-confirm="{{ __('Are you sure you want to reject this request?') }}" data-confirm-ok="{{ __('Reject') }}" data-confirm-color="red">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                {{ __('Reject') }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.manual_requests.approve', $billing) }}" class="inline" data-confirm="{{ __('Are you sure you want to approve this request?') }}" data-confirm-ok="{{ __('Approve') }}" data-confirm-color="green">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">
                {{ __('Approve') }}
            </button>
        </form>
    </div>
</div>

