<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Creator') · {{ $siteName ?? config('app.name', 'QuizWhiz') }}</title>

    @vite(['resources/css/app.css', 'resources/js/admin.js', 'resources/js/creator.js'])
    @php
        $fcm = config('services.fcm');
        $firebaseReady = !empty($fcm['api_key']) && !empty($fcm['project_id']) && !empty($fcm['messaging_sender_id']) && !empty($fcm['app_id']) && !empty($fcm['vapid_key']);
    @endphp
    @if($firebaseReady)
    <script>
        window.__FIREBASE_CONFIG__ = {
            apiKey: @json($fcm['api_key']),
            authDomain: @json($fcm['auth_domain'] ?? ''),
            projectId: @json($fcm['project_id']),
            storageBucket: @json($fcm['storage_bucket'] ?? ''),
            messagingSenderId: @json($fcm['messaging_sender_id']),
            appId: @json($fcm['app_id']),
            vapidKey: @json($fcm['vapid_key']),
        };
    </script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    @include('partials._impersonation_banner')
    <div class="min-h-screen {{ app('impersonate')->isImpersonating() ? 'pt-10' : '' }}">
        @include('partials.creator.header')

        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="py-8">
                <div class="flex flex-col gap-6 lg:flex-row">
                    @include('partials.creator.sidebar')

                    <main class="flex-1">
                        @if (session('status'))
                            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                                <div class="font-semibold">Please fix the following:</div>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('content')

                        @include('partials.creator.footer')
                    </main>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

