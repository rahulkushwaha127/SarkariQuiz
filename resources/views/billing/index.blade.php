<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('Billing') }}</h2>
        </div>
    </x-slot>

        

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Manage subscription') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                        {{ __('Status') }}: <span class="font-medium">{{ $subscription->status ?? __('none') }}</span>
                        @if($subscription?->renews_at)
                            • {{ __('Renews') }}: {{ $subscription->renews_at->format('Y-m-d') }}
                        @endif
                    </p>
                </div>
                
            </div>

            <div class="mt-4">
                @if($selectedPlan)
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $selectedPlan->name }}</h4>
                                @if($selectedPlan->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">{{ $selectedPlan->description }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ strtoupper($selectedPlan->currency) }} {{ number_format($selectedPlan->unit_amount/100,2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-500">/ {{ $selectedPlan->interval }}</p>
                            </div>
                        </div>

                        @if($selectedPlan->trial_days || $selectedPlan->users_count || $selectedPlan->teams_count || $selectedPlan->roles_count)
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @if($selectedPlan->trial_days)
                                <div class="p-3 rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Trial') }}</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedPlan->trial_days }} {{ __('days') }}</p>
                                </div>
                                @endif
                                @if($selectedPlan->users_count)
                                <div class="p-3 rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Users') }}</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedPlan->users_count }}</p>
                                </div>
                                @endif
                                @if($selectedPlan->teams_count)
                                <div class="p-3 rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Teams') }}</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedPlan->teams_count }}</p>
                                </div>
                                @endif
                                @if($selectedPlan->roles_count)
                                <div class="p-3 rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Roles') }}</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedPlan->roles_count }}</p>
                                </div>
                                @endif
                            </div>
                        @endif

                        @if(is_array($selectedPlan->features) && count($selectedPlan->features))
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">{{ __('Included features') }}</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                    @foreach($selectedPlan->features as $key => $value)
                                        @php($text = is_string($value) ? $value : (is_numeric($value) ? ucfirst(str_replace('_',' ',$key)).': '.$value : (is_array($value) ? implode(', ', array_map(fn($v)=> is_string($v)?$v:json_encode($v), $value)) : '')))
                                        @if(!empty($text))
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 text-green-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            <span>{{ $text }}</span>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-slate-400">{{ __('No plan selected. Please choose a plan first.') }}</p>
                @endif
            </div>
        </div>
        <div class="card">
            <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">{{ __('Choose payment provider') }}</h3>
            @if($selectedPlan && $canManage)
            <form method="POST" action="{{ route('billing.checkout') }}" enctype="multipart/form-data" class="space-y-3" id="billing-form">
                @csrf
                <input type="hidden" name="plan_code" value="{{ $selectedPlan->code }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($providers as $prov)
                    <label class="flex items-start gap-3 p-3 rounded-xl border hover:shadow cursor-pointer bg-white dark:bg-slate-900 dark:border-slate-800">
                        <input type="radio" name="provider" value="{{ $prov['code'] }}" class="mt-1 provider-radio" data-provider="{{ $prov['code'] }}" {{ $prov['code'] === 'manual' ? 'checked' : '' }}>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $prov['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-500">{{ $prov['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                
                <!-- Receipt upload field - only shown when manual provider is selected -->
                <div id="receipt-upload-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Upload Receipt') }} <span class="text-red-600">*</span></label>
                    <div class="flex items-center gap-3">
                        <label for="receipt_file" class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span class="text-sm">{{ __('Choose File') }}</span>
                        </label>
                        <input type="file" name="receipt" id="receipt_file" accept="image/*,.pdf" class="hidden">
                        <span id="receipt_file_name" class="text-sm text-gray-600 dark:text-slate-400"></span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-slate-500 mt-1">{{ __('Upload payment receipt (image or PDF)') }}</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">{{ __('Pay') }}</button>
                </div>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('billing-form');
                    const providerRadios = form.querySelectorAll('.provider-radio');
                    const receiptContainer = document.getElementById('receipt-upload-container');
                    const receiptFile = document.getElementById('receipt_file');
                    const receiptFileName = document.getElementById('receipt_file_name');
                    
                    providerRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === 'manual') {
                                receiptContainer.classList.remove('hidden');
                                receiptFile.setAttribute('required', 'required');
                            } else {
                                receiptContainer.classList.add('hidden');
                                receiptFile.value = '';
                                receiptFileName.textContent = '';
                                receiptFile.removeAttribute('required');
                            }
                        });
                    });
                    
                    receiptFile.addEventListener('change', function() {
                        if (this.files.length > 0) {
                            receiptFileName.textContent = this.files[0].name;
                        } else {
                            receiptFileName.textContent = '';
                        }
                    });
                    
                    // Check if manual is pre-selected
                    const manualRadio = form.querySelector('input[value="manual"]');
                    if (manualRadio && manualRadio.checked) {
                        receiptContainer.classList.remove('hidden');
                        receiptFile.setAttribute('required', 'required');
                    }
                });
            </script>
            @else
                <p class="text-sm text-gray-600 dark:text-slate-400">{{ __('Select a plan to continue.') }}</p>
            @endif
        </div>
    </div>
</x-app-layout>


