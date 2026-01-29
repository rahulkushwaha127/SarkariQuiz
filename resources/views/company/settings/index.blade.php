<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Settings') }}</h2>
        </div>
    </x-slot>

    <div x-data="{ activeSection: '{{ request()->get('section', 'branding') }}' }" class="grid grid-cols-1 lg:grid-cols-[250px_1fr] gap-6">
        <!-- Left Sidebar Navigation -->
        <div class="card p-0 overflow-hidden min-h-[30rem] h-[calc(100vh-11rem)] lg:h-[calc(100vh-11rem)] flex flex-col">
            <nav class="flex flex-col flex-1 overflow-y-auto custom-scrollbar">
                <button 
                    @click="activeSection = 'branding'" 
                    :class="activeSection === 'branding' ? 'bg-primary-600 text-white' : 'bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-700'"
                    class="w-full px-4 py-3 flex items-center justify-between transition-colors"
                >
                    <span class="font-medium">{{ __('Brand Settings') }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                
                <button 
                    @click="activeSection = 'mail'" 
                    :class="activeSection === 'mail' ? 'bg-primary-600 text-white' : 'bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-700'"
                    class="w-full px-4 py-3 flex items-center justify-between transition-colors border-t border-gray-200 dark:border-slate-700"
                >
                    <span class="font-medium">{{ __('Mail Settings') }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </nav>
        </div>

        <!-- Right Content Area -->
        <div>
            <!-- Branding Section -->
            <div x-show="activeSection === 'branding'" class="card">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Brand Settings') }}</h3>
                </div>
                
                <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="section" value="branding">
                    
                    <!-- Image Upload Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Logo Dark -->
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-xl p-6 text-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Logo Dark') }}</label>
                            <div class="mb-4 flex justify-center" id="logo_dark_preview">
                                @if(!empty($settings['logo_dark']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['logo_dark']) }}" alt="Dark Logo" class="h-20 object-contain bg-gray-900 p-3 rounded-lg">
                                @else
                                    <div class="h-20 w-20 bg-gray-900 rounded-lg flex items-center justify-center">
                                        <span class="text-white text-2xl font-bold">D</span>
                                    </div>
                                @endif
                            </div>
                            @if(!empty($settings['logo_dark']))
                                <label class="flex items-center justify-center gap-2 mb-3">
                                    <input type="checkbox" name="remove_logo_dark" value="1" class="rounded" id="remove_logo_dark">
                                    <span class="text-xs text-gray-600 dark:text-slate-400">{{ __('Remove') }}</span>
                                </label>
                            @endif
                            <label class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg cursor-pointer hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm font-medium">{{ __('Choose file here') }}</span>
                                <input type="file" name="logo_dark" accept="image/*" class="hidden" id="logo_dark_input">
                            </label>
                        </div>

                        <!-- Logo Light -->
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-xl p-6 text-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Logo Light') }}</label>
                            <div class="mb-4 flex justify-center" id="logo_light_preview">
                                @if(!empty($settings['logo_light']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['logo_light']) }}" alt="Light Logo" class="h-20 object-contain bg-white p-3 rounded-lg border">
                                @else
                                    <div class="h-20 w-20 bg-white border border-gray-300 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-900 text-2xl font-bold">D</span>
                                    </div>
                                @endif
                            </div>
                            @if(!empty($settings['logo_light']))
                                <label class="flex items-center justify-center gap-2 mb-3">
                                    <input type="checkbox" name="remove_logo_light" value="1" class="rounded" id="remove_logo_light">
                                    <span class="text-xs text-gray-600 dark:text-slate-400">{{ __('Remove') }}</span>
                                </label>
                            @endif
                            <label class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg cursor-pointer hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm font-medium">{{ __('Choose file here') }}</span>
                                <input type="file" name="logo_light" accept="image/*" class="hidden" id="logo_light_input">
                            </label>
                        </div>

                        <!-- Favicon -->
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-xl p-6 text-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Favicon') }}</label>
                            <div class="mb-4 flex justify-center" id="favicon_preview">
                                @if(!empty($settings['favicon']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['favicon']) }}" alt="Favicon" class="h-16 w-16 object-contain bg-white p-2 rounded-lg border">
                                @else
                                    <div class="h-16 w-16 bg-white border border-gray-300 rounded-lg flex items-center justify-center">
                                        <span class="text-green-500 text-2xl font-bold">D</span>
                                    </div>
                                @endif
                            </div>
                            @if(!empty($settings['favicon']))
                                <label class="flex items-center justify-center gap-2 mb-3">
                                    <input type="checkbox" name="remove_favicon" value="1" class="rounded" id="remove_favicon">
                                    <span class="text-xs text-gray-600 dark:text-slate-400">{{ __('Remove') }}</span>
                                </label>
                            @endif
                            <label class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg cursor-pointer hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm font-medium">{{ __('Choose file here') }}</span>
                                <input type="file" name="favicon" accept="image/*,.ico" class="hidden" id="favicon_input">
                            </label>
                        </div>
                    </div>

                    <!-- Text Fields -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Title Text') }}</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? $company->name) }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Footer Text') }}</label>
                            <input type="text" name="company_description" value="{{ old('company_description', $settings['company_description'] ?? $company->description ?? '') }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" placeholder="Copyright © {{ $company->name }}">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                        <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>

            <!-- Mail Settings Section -->
            <div x-show="activeSection === 'mail'" x-cloak class="card">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Mail Settings') }}</h3>
                </div>
                
                <form method="POST" action="{{ route('company.settings.update') }}">
                    @csrf
                    <input type="hidden" name="section" value="mail">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Mailer') }}</label>
                            <select name="mail_mailer" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                <option value="">{{ __('Select Mailer') }}</option>
                                <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'ses' ? 'selected' : '' }}>AWS SES</option>
                                <option value="postmark" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                <option value="resend" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'resend' ? 'selected' : '' }}>Resend</option>
                                <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="log" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Host') }}</label>
                            <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" placeholder="smtp.mailtrap.io">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Port') }}</label>
                            <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" placeholder="2525">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Encryption') }}</label>
                            <select name="mail_encryption" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                <option value="">{{ __('None') }}</option>
                                <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Username') }}</label>
                            <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Password') }}</label>
                            <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('From Address') }}</label>
                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? config('mail.from.address')) }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('From Name') }}</label>
                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('mail.from.name')) }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                        <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview handler
            function setupImagePreview(inputId, previewId, bgClass = 'bg-gray-900', imgClass = 'h-20') {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                
                if (!input || !preview) return;
                
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Show or create remove checkbox
                            const fieldName = inputId.replace('_input', '');
                            const removeCheckboxId = 'remove_' + fieldName;
                            let removeCheckbox = document.getElementById(removeCheckboxId);
                            let removeLabel = removeCheckbox ? removeCheckbox.closest('label') : null;
                            
                            if (!removeLabel) {
                                // Create remove checkbox if it doesn't exist
                                const previewContainer = preview.parentElement;
                                const buttonLabel = input.closest('label');
                                removeLabel = document.createElement('label');
                                removeLabel.className = 'flex items-center justify-center gap-2 mb-3';
                                removeLabel.innerHTML = `<input type="checkbox" name="${removeCheckboxId}" value="1" class="rounded" id="${removeCheckboxId}"><span class="text-xs text-gray-600 dark:text-slate-400">{{ __('Remove') }}</span>`;
                                previewContainer.insertBefore(removeLabel, buttonLabel);
                                removeCheckbox = document.getElementById(removeCheckboxId);
                                
                                // Setup remove checkbox handler
                                removeCheckbox.addEventListener('change', function() {
                                    const inputField = document.getElementById(inputId);
                                    const previewField = document.getElementById(previewId);
                                    
                                    if (this.checked && inputField && previewField) {
                                        // Reset preview to placeholder
                                        if (fieldName === 'logo_dark') {
                                            previewField.innerHTML = '<div class="h-20 w-20 bg-gray-900 rounded-lg flex items-center justify-center"><span class="text-white text-2xl font-bold">D</span></div>';
                                        } else if (fieldName === 'logo_light') {
                                            previewField.innerHTML = '<div class="h-20 w-20 bg-white border border-gray-300 rounded-lg flex items-center justify-center"><span class="text-gray-900 text-2xl font-bold">D</span></div>';
                                        } else if (fieldName === 'favicon') {
                                            previewField.innerHTML = '<div class="h-16 w-16 bg-white border border-gray-300 rounded-lg flex items-center justify-center"><span class="text-green-500 text-2xl font-bold">D</span></div>';
                                        }
                                        inputField.value = '';
                                        const buttonText = inputField.closest('label').querySelector('span');
                                        if (buttonText) {
                                            buttonText.textContent = '{{ __('Choose file here') }}';
                                        }
                                    }
                                });
                            } else {
                                removeLabel.style.display = 'flex';
                            }
                            
                            // Update preview
                            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="${imgClass} object-contain ${bgClass} p-3 rounded-lg${bgClass.includes('white') ? ' border' : ''}">`;
                            
                            // Update button text
                            const buttonText = input.closest('label').querySelector('span');
                            if (buttonText) {
                                buttonText.textContent = file.name;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Setup previews for all three image inputs
            setupImagePreview('logo_dark_input', 'logo_dark_preview', 'bg-gray-900', 'h-20');
            setupImagePreview('logo_light_input', 'logo_light_preview', 'bg-white', 'h-20');
            setupImagePreview('favicon_input', 'favicon_preview', 'bg-white', 'h-16 w-16');
            
            // Handle remove checkboxes
            ['remove_logo_dark', 'remove_logo_light', 'remove_favicon'].forEach(checkboxId => {
                const checkbox = document.getElementById(checkboxId);
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        const inputId = checkboxId.replace('remove_', '') + '_input';
                        const previewId = checkboxId.replace('remove_', '') + '_preview';
                        const input = document.getElementById(inputId);
                        const preview = document.getElementById(previewId);
                        
                        if (this.checked && input && preview) {
                            // Reset preview to placeholder
                            if (checkboxId === 'remove_logo_dark') {
                                preview.innerHTML = '<div class="h-20 w-20 bg-gray-900 rounded-lg flex items-center justify-center"><span class="text-white text-2xl font-bold">D</span></div>';
                            } else if (checkboxId === 'remove_logo_light') {
                                preview.innerHTML = '<div class="h-20 w-20 bg-white border border-gray-300 rounded-lg flex items-center justify-center"><span class="text-gray-900 text-2xl font-bold">D</span></div>';
                            } else if (checkboxId === 'remove_favicon') {
                                preview.innerHTML = '<div class="h-16 w-16 bg-white border border-gray-300 rounded-lg flex items-center justify-center"><span class="text-green-500 text-2xl font-bold">D</span></div>';
                            }
                            input.value = '';
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
