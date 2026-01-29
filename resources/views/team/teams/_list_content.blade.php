@php($view = $view ?? request('view','list'))
@if($view === 'cards')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($companyTeams as $t)
            <div class="card-stat">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $t->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-slate-400">{{ __('Members') }}: {{ $t->users()->count() }}</p>
                    </div>
                </div>
                @canany(['teams.update', 'teams.delete'])
                <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-slate-800">
                    @can('teams.update')
                    <a href="#" data-modal-url="{{ route('company.teams.edit', $t) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                    </a>
                    @endcan
                    @can('teams.delete')
                    <form method="POST" action="{{ route('company.teams.destroy', $t) }}" data-confirm="{{ __('Delete this team?') }}" data-confirm-ok="{{ __('Delete') }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button title="{{ __('Delete') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                        </button>
                    </form>
                    @endcan
                </div>
                @endcanany
            </div>
        @empty
            <p class="text-gray-600 dark:text-slate-400">{{ __('No teams yet.') }}</p>
        @endforelse
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-gray-600 dark:text-slate-300">
                <tr>
                    <th class="py-2">{{ __('Team') }}</th>
                    <th class="py-2">{{ __('Members') }}</th>
                    <th class="py-2 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse($companyTeams as $t)
                <tr>
                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $t->name }}</td>
                    <td class="py-3">{{ $t->users()->count() }}</td>
                    <td class="py-3">
                        @canany(['teams.update', 'teams.delete'])
                        <div class="flex gap-2 justify-end">
                            @can('teams.update')
                            <a href="#" data-modal-url="{{ route('company.teams.edit', $t) }}" title="{{ __('Edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                            </a>
                            @endcan
                            @can('teams.delete')
                            <form method="POST" action="{{ route('company.teams.destroy', $t) }}" data-confirm="{{ __('Delete this team?') }}" data-confirm-ok="{{ __('Delete') }}">
                                @csrf
                                @method('DELETE')
                                <button title="{{ __('Delete') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endcanany
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="py-3 text-gray-600 dark:text-slate-400">{{ __('No teams yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<div class="mt-4">
    <div class="async-pagination" data-async-links>
        {{ $companyTeams->withQueryString()->links() }}
    </div>
</div>


