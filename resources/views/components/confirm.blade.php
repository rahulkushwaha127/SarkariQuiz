<div id="confirm-backdrop" class="fixed inset-0 z-[99998] bg-black/50 hidden"></div>
<div id="confirm-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-gray-100 dark:border-slate-800">
        <div class="p-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Are you sure?') }}</h3>
            <p id="confirm-message" class="text-sm text-gray-600 dark:text-slate-300">{{ __('This action cannot be undone.') }}</p>
            <div class="mt-5 flex justify-end gap-2">
                <button id="confirm-cancel" type="button" class="px-4 py-2 rounded-xl border sidebar-link-inactive">{{ __('Cancel') }}</button>
                <button id="confirm-ok" type="button" class="px-4 py-2 rounded-xl bg-red-600 text-white">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
    
</div>

