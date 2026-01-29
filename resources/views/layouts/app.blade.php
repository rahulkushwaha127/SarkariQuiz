<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ sidebarOpen: false, dropdownOpen: false }" :class="{ 'dark': document.documentElement.classList.contains('dark') }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Dashboard')) - {{ $appSettings['app_name'] ?? $appSettings['company_name'] ?? config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    @if(!empty($appSettings['favicon']))
        <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($appSettings['favicon']) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950 transition-colors duration-200">
    <!-- Canvas (outer spacing) -->
    <div class="min-h-screen p-3 sm:p-4 md:p-6">
        <!-- Content Frame: Sidebar | Main -->
        <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-3 sm:gap-4 md:gap-5">
            <!-- Sidebar Column -->
            @include('layouts.sidebar')

            <!-- Main Column -->
            <div class="flex flex-col gap-6 min-h-[calc(100vh-3rem)]">
                <!-- Header Bar (elevated) -->
                <div class="rounded-2xl elevated">
                    @include('layouts.topbar')
                </div>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto custom-scrollbar">
                @if (isset($header))
                    <div class="mb-6">
                        <div class="card">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <div>
                    {{ $slot }}
                </div>
                </main>

                <!-- Footer -->
                @include('layouts.footer')
            </div>
        </div>
    </div>

    <!-- Language Switch Form (Hidden) -->
    <form id="language-form" method="POST" action="{{ route('locale.switch', ':locale') }}" style="display: none;">
        @csrf
        <input type="hidden" id="language-input" name="locale" value="">
    </form>

    <!-- Global AJAX Modal -->
    <x-modal name="ajax-modal" :show="false" maxWidth="2xl">
        <div id="ajax-modal-body"></div>
    </x-modal>

    {{-- Toasts --}}
    <x-toast />
    {{-- Confirm dialog --}}
    <x-confirm />

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
