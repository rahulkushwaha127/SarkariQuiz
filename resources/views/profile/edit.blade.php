<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Profile') }}
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Update your account profile information and settings') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card border-red-200 dark:border-red-800">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
