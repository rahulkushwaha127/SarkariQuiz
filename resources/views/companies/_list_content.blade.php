@php($view = $view ?? request('view', 'list'))
@if($view === 'list')
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-gray-600 dark:text-slate-300">
                <tr>
                    <th class="py-2">{{ __('Name') }}</th>
                    <th class="py-2">{{ __('Owner') }}</th>
                    <th class="py-2">{{ __('Email') }}</th>
                    <th class="py-2">{{ __('Status') }}</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse($teams as $team)
                <tr>
                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $team->name }}</td>
                    <td class="py-3 text-gray-600 dark:text-slate-400">{{ $team->owner?->name ?? '—' }}</td>
                    <td class="py-3 text-gray-600 dark:text-slate-400">{{ $team->owner?->email ?? '—' }}</td>
                    <td class="py-3">
                        @if($team->is_active)
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('Active') }}</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('Disabled') }}</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2 justify-end">
                            <form method="POST" action="{{ route('companies.toggle_active', $team) }}" data-confirm="{{ $team->is_active ? __('Disable this company?') : __('Enable this company?') }}" data-confirm-ok="{{ $team->is_active ? __('Disable') : __('Enable') }}" data-confirm-color="{{ $team->is_active ? 'red' : 'green' }}">
                                @csrf
                                <button type="submit" title="{{ $team->is_active ? __('Disable Company') : __('Enable Company') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border shadow-sm {{ $team->is_active ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/40 hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-900/40 hover:bg-green-100 dark:hover:bg-green-900/30 hover:shadow-md' }} transition-all">
                                    @if($team->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    @endif
                                </button>
                            </form>
                            <a href="#" data-modal-url="{{ route('companies.edit', $team) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('companies.destroy', $team) }}" data-confirm="{{ __('Delete this company?') }}" data-confirm-ok="{{ __('Delete') }}">
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
                <tr><td colspan="5" class="py-3 text-gray-600 dark:text-slate-400">{{ __('No companies yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teams as $team)
            <div class="card-stat">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-2">{{ __('Company') }}</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate mb-2">{{ $team->name }}</h3>
                        <div class="space-y-1">
                            <p class="text-xs text-gray-600 dark:text-slate-400">
                                <span class="font-medium">{{ __('Owner') }}:</span> 
                                <span class="text-gray-900 dark:text-white">{{ $team->owner?->name ?? '—' }}</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-slate-500 truncate">{{ $team->owner?->email ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800">
                    <div>
                        @if($team->is_active)
                            <span class="text-xs px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('Active') }}</span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('Disabled') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('companies.toggle_active', $team) }}" data-confirm="{{ $team->is_active ? __('Disable this company?') : __('Enable this company?') }}" data-confirm-ok="{{ $team->is_active ? __('Disable') : __('Enable') }}" data-confirm-color="{{ $team->is_active ? 'red' : 'green' }}">
                            @csrf
                            <button type="submit" title="{{ $team->is_active ? __('Disable Company') : __('Enable Company') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border shadow-sm {{ $team->is_active ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/40 hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-900/40 hover:bg-green-100 dark:hover:bg-green-900/30 hover:shadow-md' }} transition-all">
                                @if($team->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                @endif
                                <span class="sr-only">{{ $team->is_active ? __('Disable') : __('Enable') }}</span>
                            </button>
                        </form>
                        <a href="#" data-modal-url="{{ route('companies.edit', $team) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                            <span class="sr-only">Edit</span>
                        </a>
                        <form method="POST" action="{{ route('companies.destroy', $team) }}" data-confirm="{{ __('Delete this company?') }}" data-confirm-ok="{{ __('Delete') }}">
                            @csrf
                            @method('DELETE')
                            <button title="{{ __('Delete') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                                <span class="sr-only">Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600 dark:text-slate-400">{{ __('No companies yet.') }}</p>
        @endforelse
    </div>
@endif

<div class="mt-4">
    <div class="async-pagination" data-async-links>
        {{ $teams->withQueryString()->links() }}
    </div>
    {{-- Pagination links intercepted via JS --}}
    
    
</div>


