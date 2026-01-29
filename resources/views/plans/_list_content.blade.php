@php($view = $view ?? request('view','list'))
@if($view === 'cards')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $p)
            <div class="card-stat">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $p->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-slate-400">
                            {{ strtoupper($p->currency) }} {{ number_format($p->unit_amount/100, 2) }} / {{ $p->interval }}
                        </p>
                    </div>
                    @if($p->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                            {{ __('Active') }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('plans.orders', $p) }}" title="{{ __('Orders') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900/40 shadow-sm hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </a>
                    <a href="#" data-modal-url="{{ route('plans.edit', $p) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('plans.destroy', $p) }}" data-confirm="{{ __('Delete this plan?') }}" data-confirm-ok="{{ __('Delete') }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button title="{{ __('Delete') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-600 dark:text-slate-400">{{ __('No plans yet.') }}</p>
        @endforelse
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-gray-600 dark:text-slate-300">
                <tr>
                    <th class="py-2">{{ __('Name') }}</th>
                    <th class="py-2">{{ __('Price') }}</th>
                    <th class="py-2">{{ __('Interval') }}</th>
                    <th class="py-2">{{ __('Active') }}</th>
                    <th class="py-2 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse($plans as $p)
                <tr>
                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $p->name }}</td>
                    <td class="py-3">{{ strtoupper($p->currency) }} {{ number_format($p->unit_amount/100, 2) }}</td>
                    <td class="py-3">{{ $p->interval }}</td>
                    <td class="py-3">{{ $p->is_active ? __('Yes') : __('No') }}</td>
                    <td class="py-3">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('plans.orders', $p) }}" title="{{ __('Orders') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900/40 shadow-sm hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            <a href="#" data-modal-url="{{ route('plans.edit', $p) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('plans.destroy', $p) }}" data-confirm="{{ __('Delete this plan?') }}" data-confirm-ok="{{ __('Delete') }}">
                                @csrf
                                @method('DELETE')
                                <button title="{{ __('Delete') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-3 text-gray-600 dark:text-slate-400">{{ __('No plans yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<div class="mt-4">
    <div class="async-pagination" data-async-links>
        {{ $plans->withQueryString()->links() }}
    </div>
</div>


