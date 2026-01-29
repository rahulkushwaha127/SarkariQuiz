<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Manual Plan Requests') }}</h2>
        </div>
    </x-slot>

    <div class="card">
        <div id="requests-list" data-async-list data-method="get" data-src="{{ route('admin.manual_requests.index') }}">
            @include('admin.manual_requests._list_content', ['orders' => $orders])
        </div>
    </div>
</x-app-layout>

