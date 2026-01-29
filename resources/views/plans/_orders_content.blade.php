<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="text-left text-gray-600 dark:text-slate-300">
            <tr>
                <th class="py-2">{{ __('Invoice') }}</th>
                <th class="py-2">{{ __('Company') }}</th>
                <th class="py-2">{{ __('Amount') }}</th>
                <th class="py-2">{{ __('Status') }}</th>
                <th class="py-2">{{ __('Provider') }}</th>
                <th class="py-2">{{ __('Date') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($orders as $order)
            <tr>
                <td class="py-3 font-medium text-gray-900 dark:text-white">
                    @if(!empty($order->ref_url))
                        <a href="{{ $order->ref_url }}" target="_blank" rel="noreferrer" class="text-primary-600 hover:underline">
                            {{ $order->invoice_number ?? __('Receipt') }}
                        </a>
                    @else
                        {{ $order->invoice_number ?? '—' }}
                    @endif
                </td>
                <td class="py-3 text-gray-600 dark:text-slate-400">
                    {{ $order->company->name ?? __('Unknown') }}
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
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-10 text-center text-gray-600 dark:text-slate-400">
                    {{ __('No orders found for this plan.') }}
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

