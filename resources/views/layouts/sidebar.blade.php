<!-- Mobile Overlay -->
<div 
    x-show="sidebarOpen && window.innerWidth < 1024"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
    @click="if (window.innerWidth < 1024) sidebarOpen = false"
></div>

<aside 
    class="fixed lg:relative inset-y-0 left-0 z-50 lg:z-auto flex flex-col rounded-2xl lg:rounded-2xl elevated h-screen lg:h-[calc(100vh-3rem)] p-4 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
    :class="sidebarOpen || window.innerWidth >= 1024 ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    x-data="{ openMenus: {} }"
>
    <!-- Brand -->
    <div class="flex items-center justify-center h-16 px-4 relative">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
            @if(!empty($appSettings['logo_dark']))
                {{-- Logo Dark (for light mode) --}}
                <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_dark']) }}" alt="Logo" class="h-9 w-auto object-contain dark:hidden">
                @if(!empty($appSettings['logo_light']))
                    {{-- Logo Light (for dark mode) --}}
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_light']) }}" alt="Logo" class="h-9 w-auto object-contain hidden dark:block">
                @else
                    {{-- Fallback to dark logo if light logo not set --}}
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_dark']) }}" alt="Logo" class="h-9 w-auto object-contain hidden dark:block opacity-80">
                @endif
            @else
                {{-- Default icon fallback --}}
                <div class="w-9 h-9 bg-slate-900 dark:bg-slate-800 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l8 4v6c0 5-8 8-8 8s-8-3-8-8V7l8-4z"/>
                    </svg>
                </div>
            @endif
        </a>
        <!-- Close Button (Mobile Only) -->
        <button 
            @click="sidebarOpen = false"
            class="lg:hidden absolute right-4 p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
            aria-label="{{ __('Close sidebar') }}"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <hr class="mx-4 border-gray-200 dark:border-slate-800">

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="flex-1">{{ __('Dashboard') }}</span>
        </a>

        @if (Auth::user()->is_super_admin)
        <!-- Companies (Superadmin) -->
        <a href="{{ route('companies.index') }}" 
           class="sidebar-link {{ request()->routeIs('companies.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4m0 6l9 4 9-4"/>
                </svg>
            <span class="flex-1">{{ __('Companies') }}</span>
        </a>

        <!-- Plans (Superadmin) -->
        <a href="{{ route('plans.index') }}" 
           class="sidebar-link {{ request()->routeIs('plans.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span class="flex-1">{{ __('Plans') }}</span>
        </a>

        <!-- Orders (Superadmin) -->
        <a href="{{ route('orders.index') }}" 
           class="sidebar-link {{ request()->routeIs('orders.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="flex-1">{{ __('Orders') }}</span>
        </a>

        <!-- Manual Plan Requests (Superadmin) -->
        <a href="{{ route('admin.manual_requests.index') }}" 
           class="sidebar-link {{ request()->routeIs('admin.manual_requests.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="flex-1">{{ __('Manual Requests') }}</span>
        </a>

        <!-- API Docs (Superadmin) -->
        <a href="{{ route('admin.api-docs.index') }}" 
           class="sidebar-link {{ request()->routeIs('admin.api-docs.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="flex-1">{{ __('API Docs') }}</span>
        </a>

        <!-- Settings (Superadmin) -->
        <a href="{{ route('admin.settings.index') }}" 
           class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="flex-1">{{ __('Settings') }}</span>
        </a>
        @endif

        @hasanyrole('Owner|Member')
        <!-- Users (Company) -->
        <a href="{{ route('team.users.index') }}" 
           class="sidebar-link {{ request()->routeIs('team.users.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            <span class="flex-1">{{ __('Users') }}</span>
        </a>

        <!-- Teams (Company groups) -->
        <a href="{{ route('company.teams.index') }}" 
           class="sidebar-link {{ request()->routeIs('company.teams.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h7v7H4V6zm9 0h7v7h-7V6zM4 15h7v3H4v-3zm9 0h7v3h-7v-3z"/>
            </svg>
            <span class="flex-1">{{ __('Teams') }}</span>
        </a>

        @role('Owner')
        <!-- Roles (Company) -->
        <a href="{{ route('team.roles.index') }}" 
           class="sidebar-link {{ request()->routeIs('team.roles.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 2c-2.761 0-5 1.791-5 4v1h10v-1c0-2.209-2.239-4-5-4z"/>
            </svg>
            <span class="flex-1">{{ __('Roles') }}</span>
        </a>

        <!-- Billing link removed per request -->
        <a href="{{ route('billing.choose') }}" 
           class="sidebar-link {{ request()->routeIs('billing.choose') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span class="flex-1">{{ __('Subscription') }}</span>
        </a>

        <!-- Company Orders -->
        <a href="{{ route('company.orders.index') }}" 
           class="sidebar-link {{ request()->routeIs('company.orders.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="flex-1">{{ __('Orders') }}</span>
        </a>

        <!-- Settings (Company) -->
        <a href="{{ route('company.settings.index') }}" 
           class="sidebar-link {{ request()->routeIs('company.settings.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="flex-1">{{ __('Settings') }}</span>
        </a>
        @endrole
        @endhasanyrole
    </nav>

    <!-- Bottom Section: User -->
    <div class="px-6 py-6 mt-auto">
        <div class="flex items-center">
            <div class="relative">
                <div class="w-11 h-11 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
            </div>
            <div class="ml-3 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>

