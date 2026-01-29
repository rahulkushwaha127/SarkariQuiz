<header class="h-16 flex items-center justify-between px-3 sm:px-6 sticky top-0 z-30 bg-white dark:bg-slate-900 rounded-2xl overflow-visible">
    <!-- Mobile Menu Button -->
    <button 
        @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        x-show="true"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Search Bar -->
    <div class="hidden md:flex flex-1 max-w-md mx-8">
        <div class="relative w-full">
            <input 
                type="text" 
                placeholder="{{ __('Search') }}..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 text-gray-900 dark:text-slate-100 placeholder-gray-500 dark:placeholder-slate-400 transition-all"
            >
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400 dark:text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-2 sm:space-x-4">
        <!-- Language Switcher -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open"
                class="p-2 rounded-lg text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                title="{{ __('Select Language') }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </button>

            <div 
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
                class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-48 sm:w-56 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-2 z-[60]"
            >
                @foreach(config('app.available_locales') as $locale)
                    <a 
                        href="{{ route('locale.switch', $locale) }}"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ app()->getLocale() === $locale ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : '' }}"
                    >
                        @switch($locale)
                            @case('en')
                                🇬🇧 English
                                @break
                            @case('es')
                                🇪🇸 Español
                                @break
                            @case('fr')
                                🇫🇷 Français
                                @break
                            @case('de')
                                🇩🇪 Deutsch
                                @break
                            @case('ar')
                                🇸🇦 العربية
                                @break
                            @default
                                {{ strtoupper($locale) }}
                        @endswitch
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Theme Toggle -->
        <button 
            onclick="toggleTheme()"
            class="p-2 rounded-lg text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
            title="{{ __('Theme') }}"
        >
            <!-- Sun Icon (Light Mode - shown when NOT in dark mode) -->
            <svg id="theme-light-icon" class="w-5 h-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
            </svg>
            <!-- Moon Icon (Dark Mode - shown when IN dark mode) -->
            <svg id="theme-dark-icon" class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
            </svg>
        </button>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: dropdownOpen }">
            <button 
                @click="dropdownOpen = !dropdownOpen"
                class="flex items-center space-x-3 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
            >
                <div class="w-9 h-9 bg-primary-600 dark:bg-primary-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div 
                x-show="dropdownOpen"
                @click.away="dropdownOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
                class="absolute right-0 mt-2 w-56 sm:w-64 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-2 z-[60]"
            >
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Profile') }}
                </a>
                @if(Auth::user()->is_super_admin)
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        {{ __('Settings') }}
                    </a>
                @elseif(Auth::user()->hasRole('Owner'))
                    <a href="{{ route('company.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        {{ __('Settings') }}
                    </a>
                @endif
                <hr class="my-2 border-gray-200 dark:border-slate-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

