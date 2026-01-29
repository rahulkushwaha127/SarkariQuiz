<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
            <thead class="text-left text-gray-600 dark:text-slate-300">
                <tr>
                    <th class="py-2">{{ __('Invoice') }}</th>
                    <th class="py-2">{{ __('Company') }}</th>
                    <th class="py-2">{{ __('Plan') }}</th>
                    <th class="py-2">{{ __('Amount') }}</th>
                    <th class="py-2">{{ __('Status') }}</th>
                    <th class="py-2">{{ __('Provider') }}</th>
                    <th class="py-2">{{ __('Date') }}</th>
                    <th class="py-2">{{ __('Receipt') }}</th>
                </tr>
            </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($orders as $order)
            <tr>
                <td class="py-3 font-medium text-gray-900 dark:text-white">
                    {{ $order->invoice_number ?? '—' }}
                </td>
                <td class="py-3 text-gray-600 dark:text-slate-400">
                    {{ $order->company->name ?? __('Unknown') }}
                </td>
                <td class="py-3 text-gray-600 dark:text-slate-400">
                    @if($order->plan_code)
                        @php($plan = \App\Models\Plan::where('code', $order->plan_code)->first())
                        {{ $plan->name ?? $order->plan_code }}
                    @else
                        —
                    @endif
                </td>
                <td class="py-3 font-medium text-gray-900 dark:text-white">
                    {{ strtoupper($order->currency ?? 'USD') }} {{ number_format(($order->amount ?? 0) / 100, 2) }}
                </td>
                <td class="py-3">
                    @if($order->tx_status === 'succeeded')
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('Succeeded') }}</span>
                    @elseif($order->tx_status === 'failed')
                        @if($order->provider === 'manual')
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('Rejected') }}</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('Failed') }}</span>
                        @endif
                    @elseif($order->tx_status === 'pending')
                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">{{ __('Pending') }}</span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300">{{ $order->tx_status ?? '—' }}</span>
                    @endif
                </td>
                <td class="py-3 text-gray-600 dark:text-slate-400">
                    <span class="capitalize">{{ $order->provider ?? '—' }}</span>
                </td>
                <td class="py-3 text-gray-600 dark:text-slate-400">
                    {{ $order->occurred_at ? $order->occurred_at->format('Y-m-d H:i') : '—' }}
                </td>
                <td class="py-3">
                    @if($order->ref_url)
                        <a href="{{ $order->ref_url }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-900/40 shadow-sm hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:shadow-md transition-all" title="{{ __('View Receipt') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                    @else
                        —
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-10 text-center text-gray-600 dark:text-slate-400">
                    {{ __('No orders found.') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <div class="async-pagination" data-async-links>
        {{ $orders->withQueryString()->links() }}
    </div>
</div>

