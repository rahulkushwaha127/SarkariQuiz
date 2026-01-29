<footer class="mt-auto pt-6">
    <div class="card py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4">
                @if(!empty($appSettings['logo_dark']))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_dark']) }}" alt="Logo" class="h-6 w-auto object-contain dark:hidden">
                    @if(!empty($appSettings['logo_light']))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_light']) }}" alt="Logo" class="h-6 w-auto object-contain hidden dark:block">
                    @else
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings['logo_dark']) }}" alt="Logo" class="h-6 w-auto object-contain hidden dark:block opacity-80">
                    @endif
                @endif
                @if(!empty($appSettings['app_description']) || !empty($appSettings['company_description']))
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400">
                            {{ $appSettings['app_description'] ?? $appSettings['company_description'] }}
                        </p>
                    </div>
                @endif
            </div>
            @if(empty($appSettings['app_description']) && empty($appSettings['company_description']))
                <div class="text-sm text-gray-500 dark:text-slate-500">
                    <p>&copy; {{ date('Y') }} {{ $appSettings['app_name'] ?? $appSettings['company_name'] ?? config('app.name') }}. {{ __('All rights reserved.') }}</p>
                </div>
            @endif
        </div>
    </div>
</footer>

