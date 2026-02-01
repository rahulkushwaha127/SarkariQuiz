<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') · {{ $siteName ?? config('app.name', 'QuizWhiz') }}</title>

    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        @include('partials.admin.header')

        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="py-8">
                <div class="flex flex-col gap-6 lg:flex-row">
                    @include('partials.admin.sidebar')

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

                        @include('partials.admin.footer')
                    </main>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast container --}}
    <div id="toast-container" class="fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2 px-4 sm:px-0"></div>

    {{-- Common AJAX modal --}}
    <div id="common-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/60" data-modal-close="true"></div>
        <div class="relative mx-auto flex min-h-full max-w-2xl items-center justify-center p-4">
            <div class="flex max-h-[90vh] w-full flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <h2 class="modal-title text-base font-semibold text-slate-900">Modal</h2>
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-modal-close="true">
                        Close
                    </button>
                </div>
                <div class="modal-body max-h-[calc(90vh-80px)] overflow-y-auto px-5 py-4">
                    <div class="text-sm text-slate-600">Loading…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirm modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/60" data-delete-close="true"></div>
        <div class="relative mx-auto flex min-h-full max-w-lg items-center justify-center p-4">
            <div class="w-full overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Confirm delete</h2>
                    <p class="mt-1 text-sm text-slate-600">This action cannot be undone.</p>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-delete-close="true">
                        Cancel
                    </button>
                    <form id="deleteItem" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

