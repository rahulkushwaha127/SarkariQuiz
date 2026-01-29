@php($view = $view ?? request('view', 'list'))
@if($view === 'cards')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($members as $member)
            <div class="card-stat">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-white flex-shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $member->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-slate-400 truncate">{{ $member->email }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 capitalize font-medium">{{ $member->pivot->role ?? 'member' }}</span>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('users.toggle_active', $member) }}" data-confirm="{{ $member->is_active ? __('Disable login for :name?', ['name' => $member->name]) : __('Enable login for :name?', ['name' => $member->name]) }}" data-confirm-ok="{{ $member->is_active ? __('Disable') : __('Enable') }}" data-confirm-color="{{ $member->is_active ? 'red' : 'green' }}">
                            @csrf
                            <button type="submit" title="{{ $member->is_active ? __('Login Disable') : __('Login Enable') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border shadow-sm {{ $member->is_active ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/40 hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-900/40 hover:bg-green-100 dark:hover:bg-green-900/30 hover:shadow-md' }} transition-all">
                                @if($member->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                @endif
                            </button>
                        </form>
                        <a href="#" data-modal-url="{{ route('team.users.edit', $member) }}" title="Edit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('team.users.destroy', $member) }}" data-confirm="{{ __('Remove this user from the team?') }}" data-confirm-ok="{{ __('Delete') }}">
                            @csrf
                            @method('DELETE')
                            <button title="Remove" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600 dark:text-slate-400">No users in this team.</p>
        @endforelse
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-gray-600 dark:text-slate-300">
                <tr>
                    <th class="py-2">Name</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Role</th>
                    <th class="py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse($members as $member)
                <tr>
                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $member->name }}</td>
                    <td class="py-3 text-gray-600 dark:text-slate-400">{{ $member->email }}</td>
                    <td class="py-3"><span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 capitalize">{{ $member->pivot->role ?? 'member' }}</span></td>
                    <td class="py-3">
                        <div class="flex gap-2 justify-end">
                            <form method="POST" action="{{ route('users.toggle_active', $member) }}" data-confirm="{{ $member->is_active ? __('Disable login for :name?', ['name' => $member->name]) : __('Enable login for :name?', ['name' => $member->name]) }}" data-confirm-ok="{{ $member->is_active ? __('Disable') : __('Enable') }}" data-confirm-color="{{ $member->is_active ? 'red' : 'green' }}">
                                @csrf
                                <button type="submit" title="{{ $member->is_active ? __('Login Disable') : __('Login Enable') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border shadow-sm {{ $member->is_active ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/40 hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-900/40 hover:bg-green-100 dark:hover:bg-green-900/30 hover:shadow-md' }} transition-all">
                                    @if($member->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    @endif
                                </button>
                            </form>
                            <a href="#" data-modal-url="{{ route('team.users.edit', $member) }}" title="Edit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a3 3 0 10-4.243-4.243L3.757 15.757 4 20z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('team.users.destroy', $member) }}" data-confirm="{{ __('Remove this user from the team?') }}" data-confirm-ok="{{ __('Delete') }}">
                                @csrf
                                @method('DELETE')
                                <button title="Remove" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/40 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 hover:shadow-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h8V5a1 1 0 00-1-1z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-3 text-gray-600 dark:text-slate-400">No users in this team.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<div class="mt-4">
    <div class="async-pagination" data-async-links>
        {{ $members->withQueryString()->links() }}
    </div>
</div>


