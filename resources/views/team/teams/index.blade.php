<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row items-center justify-between gap-4">
            <div class="flex-shrink-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Teams') }}</h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-slate-400">{{ __('Company Teams') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <form method="GET" action="{{ route('company.teams.index') }}" data-async-target="#company-teams-list" class="hidden md:flex items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search teams') }}" class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-sm">
                </form>
                <a href="#" data-async-toggle-view data-async-target="#company-teams-list" title="{{ __('Toggle view') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border">
                    <span data-icon="toggle"></span>
                </a>
                @can('teams.create')
                <a href="#" data-modal-url="{{ route('company.teams.create') }}" title="{{ __('Add Team') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-600 hover:bg-primary-700 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="sr-only">{{ __('Add Team') }}</span>
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div id="company-teams-list" data-async-list data-method="post" data-src="{{ route('company.teams.filter') }}" data-view="{{ request('view','list') }}">
            <div class="py-10 flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-app-layout>


