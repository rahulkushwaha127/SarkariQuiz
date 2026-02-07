@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Settings</h1>
            <p class="mt-1 text-sm text-slate-600">Configure app and front-end visibility.</p>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-slate-200">
            <nav class="-mb-px flex flex-wrap gap-6" aria-label="Settings sections">
                <button type="button"
                        class="settings-tab border-b-2 border-indigo-500 px-1 py-3 text-sm font-medium text-indigo-600"
                        data-tab="general">
                    General
                </button>
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="ads">
                    Ads
                </button>
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="notifications">
                    Notifications
                </button>
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="menu">
                    Front-end menu
                </button>
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="payments">
                    Payments
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Tab: General --}}
            <div id="panel-general" class="settings-panel rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">General</h2>
                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700">Site name</label>
                    <input name="site_name" value="{{ old('site_name', $values['site_name']) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('site_name') border-red-300 @enderror"
                           placeholder="QuizWhiz">
                    @error('site_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Tab: Ads --}}
            <div id="panel-ads" class="settings-panel hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Ads</h2>
                <p class="mt-1 text-xs text-slate-600">Scaffold only (placeholders now, real ads later).</p>

                <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Ads enabled</div>
                        <div class="text-xs text-slate-600">Master switch for all ad placements.</div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="ads_enabled" value="1"
                               class="h-4 w-4 rounded border-slate-300"
                               @checked(old('ads_enabled', $values['ads_enabled']) == '1')>
                        Enabled
                    </label>
                </div>

                <div class="mt-4 rounded-xl border border-slate-200 p-4">
                    <div class="text-sm font-semibold text-slate-900">Ad placements</div>
                    <div class="mt-1 text-xs text-slate-600">No ads on question screens. Interstitial only on result screens.</div>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ads_banner_enabled" value="1"
                                   class="h-4 w-4 rounded border-slate-300"
                                   @checked(old('ads_banner_enabled', $values['ads_banner_enabled']) == '1')>
                            Banner
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ads_interstitial_enabled" value="1"
                                   class="h-4 w-4 rounded border-slate-300"
                                   @checked(old('ads_interstitial_enabled', $values['ads_interstitial_enabled']) == '1')>
                            Interstitial (after result)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ads_rewarded_enabled" value="1"
                                   class="h-4 w-4 rounded border-slate-300"
                                   @checked(old('ads_rewarded_enabled', $values['ads_rewarded_enabled']) == '1')>
                            Rewarded (optional)
                        </label>
                    </div>
                    <div class="mt-4">
                        <label class="text-sm font-medium text-slate-700">Show interstitial every N results</label>
                        <input type="number" min="1" max="20"
                               name="ads_interstitial_every_n_results"
                               value="{{ old('ads_interstitial_every_n_results', $values['ads_interstitial_every_n_results']) }}"
                               class="mt-1 w-full max-w-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('ads_interstitial_every_n_results') border-red-300 @enderror">
                        @error('ads_interstitial_every_n_results') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Tab: Notifications --}}
            <div id="panel-notifications" class="settings-panel hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Daily reminder time (HH:MM)</label>
                        <input name="daily_reminder_time" value="{{ old('daily_reminder_time', $values['daily_reminder_time']) }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('daily_reminder_time') border-red-300 @enderror"
                               placeholder="07:00">
                        @error('daily_reminder_time') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Contest reminder lead (minutes)</label>
                        <input type="number" min="5" max="180"
                               name="contest_reminder_lead_minutes" value="{{ old('contest_reminder_lead_minutes', $values['contest_reminder_lead_minutes']) }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('contest_reminder_lead_minutes') border-red-300 @enderror">
                        @error('contest_reminder_lead_minutes') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Tab: Front-end menu --}}
            <div id="panel-menu" class="settings-panel hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Front-end menu</h2>
                <p class="mt-1 text-sm text-slate-600">Enable or hide sidebar menu items for students and guests.</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach(array_keys($values['frontend_menu']) as $key)
                        @php
                            $label = ucfirst(str_replace('_', ' ', $key));
                            $enabled = old('menu_' . $key, $values['frontend_menu'][$key] ?? true);
                        @endphp
                        <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50">
                            <input type="hidden" name="menu_{{ $key }}" value="0">
                            <input type="checkbox" name="menu_{{ $key }}" value="1"
                                   class="h-4 w-4 rounded border-slate-300"
                                   @checked($enabled)>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Tab: Payments --}}
            <div id="panel-payments" class="settings-panel hidden space-y-5">
                @php
                    $pv = $values['payment'] ?? [];
                    $activeGateway = old('payment_active_gateway', $pv['payment_active_gateway'] ?? 'razorpay');
                    $paymentMode   = old('payment_mode', $pv['payment_mode'] ?? 'sandbox');
                @endphp

                {{-- Active Gateway & Mode Toggle --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Payment Gateway</h2>
                    <p class="mt-1 text-xs text-slate-600">Choose the active gateway and environment mode.</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        {{-- Active gateway --}}
                        <div>
                            <label class="text-sm font-medium text-slate-700">Active gateway</label>
                            <select name="payment_active_gateway"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                <option value="razorpay" @selected($activeGateway === 'razorpay')>Razorpay</option>
                                <option value="phonepe" @selected($activeGateway === 'phonepe')>PhonePe</option>
                            </select>
                        </div>

                        {{-- Sandbox / Live toggle --}}
                        <div>
                            <label class="text-sm font-medium text-slate-700">Environment</label>
                            <div class="mt-2 flex items-center gap-3">
                                <span class="text-sm text-slate-600" id="mode-label-sandbox">Sandbox</span>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="hidden" name="payment_mode" value="sandbox" id="payment-mode-hidden">
                                    <input type="checkbox" id="payment-mode-toggle" class="peer sr-only"
                                           @checked($paymentMode === 'live')>
                                    <div class="peer h-6 w-11 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-emerald-500 peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                                </label>
                                <span class="text-sm text-slate-600" id="mode-label-live">Live</span>
                            </div>
                            <p class="mt-1 text-xs text-amber-600" id="live-warning" style="{{ $paymentMode === 'live' ? '' : 'display:none' }}">
                                Live mode is active. Real transactions will be processed.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Gateway sub-tabs: Razorpay | PhonePe --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 pt-4">
                        <nav class="-mb-px flex gap-6" aria-label="Payment gateways">
                            <button type="button"
                                    class="pg-tab border-b-2 border-indigo-500 px-1 pb-3 text-sm font-medium text-indigo-600"
                                    data-pg="razorpay">
                                Razorpay
                            </button>
                            <button type="button"
                                    class="pg-tab border-b-2 border-transparent px-1 pb-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                                    data-pg="phonepe">
                                PhonePe
                            </button>
                        </nav>
                    </div>

                    {{-- Razorpay credentials --}}
                    <div id="pg-panel-razorpay" class="pg-panel p-5 space-y-6">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">S</span>
                                Sandbox credentials
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">Test mode keys from your Razorpay dashboard.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Key ID</label>
                                    <input type="text" name="razorpay_sandbox_key_id"
                                           value="{{ old('razorpay_sandbox_key_id', $pv['razorpay_sandbox_key_id'] ?? '') }}"
                                           placeholder="rzp_test_..."
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Key Secret</label>
                                    <input type="password" name="razorpay_sandbox_key_secret"
                                           value=""
                                           placeholder="{{ ($pv['razorpay_sandbox_key_secret'] ?? '') !== '' ? 'Leave blank to keep current' : 'Enter secret' }}"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-5">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">L</span>
                                Live credentials
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">Production keys for real payments.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Key ID</label>
                                    <input type="text" name="razorpay_live_key_id"
                                           value="{{ old('razorpay_live_key_id', $pv['razorpay_live_key_id'] ?? '') }}"
                                           placeholder="rzp_live_..."
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Key Secret</label>
                                    <input type="password" name="razorpay_live_key_secret"
                                           value=""
                                           placeholder="{{ ($pv['razorpay_live_key_secret'] ?? '') !== '' ? 'Leave blank to keep current' : 'Enter secret' }}"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PhonePe credentials --}}
                    <div id="pg-panel-phonepe" class="pg-panel hidden p-5 space-y-6">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">S</span>
                                Sandbox credentials
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">UAT credentials from PhonePe developer portal.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client ID</label>
                                    <input type="text" name="phonepe_sandbox_client_id"
                                           value="{{ old('phonepe_sandbox_client_id', $pv['phonepe_sandbox_client_id'] ?? '') }}"
                                           placeholder="PGTESTPAYUAT..."
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client Secret</label>
                                    <input type="password" name="phonepe_sandbox_client_secret"
                                           value=""
                                           placeholder="{{ ($pv['phonepe_sandbox_client_secret'] ?? '') !== '' ? 'Leave blank to keep current' : 'Enter secret' }}"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client Version</label>
                                    <input type="text" name="phonepe_sandbox_client_version"
                                           value="{{ old('phonepe_sandbox_client_version', $pv['phonepe_sandbox_client_version'] ?? '1') }}"
                                           placeholder="1"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-5">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">L</span>
                                Live credentials
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">Production credentials for real payments.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client ID</label>
                                    <input type="text" name="phonepe_live_client_id"
                                           value="{{ old('phonepe_live_client_id', $pv['phonepe_live_client_id'] ?? '') }}"
                                           placeholder="PGTESTPAYLIVE..."
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client Secret</label>
                                    <input type="password" name="phonepe_live_client_secret"
                                           value=""
                                           placeholder="{{ ($pv['phonepe_live_client_secret'] ?? '') !== '' ? 'Leave blank to keep current' : 'Enter secret' }}"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-700">Client Version</label>
                                    <input type="text" name="phonepe_live_client_version"
                                           value="{{ old('phonepe_live_client_version', $pv['phonepe_live_client_version'] ?? '1') }}"
                                           placeholder="1"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save settings
                </button>
            </div>
        </form>
    </div>

    <script>
        (function() {
            /* ── Main settings tabs ── */
            var tabs = document.querySelectorAll('.settings-tab');
            var panels = document.querySelectorAll('.settings-panel');
            function showTab(tabId) {
                tabs.forEach(function(t) {
                    var isActive = t.getAttribute('data-tab') === tabId;
                    t.classList.toggle('border-indigo-500', isActive);
                    t.classList.toggle('text-indigo-600', isActive);
                    t.classList.toggle('border-transparent', !isActive);
                    t.classList.toggle('text-slate-500', !isActive);
                });
                panels.forEach(function(p) {
                    p.classList.toggle('hidden', p.id !== 'panel-' + tabId);
                });
            }
            tabs.forEach(function(t) {
                t.addEventListener('click', function() {
                    showTab(t.getAttribute('data-tab'));
                });
            });

            /* ── Payment gateway sub-tabs ── */
            var pgTabs = document.querySelectorAll('.pg-tab');
            var pgPanels = document.querySelectorAll('.pg-panel');
            function showPg(pgId) {
                pgTabs.forEach(function(t) {
                    var isActive = t.getAttribute('data-pg') === pgId;
                    t.classList.toggle('border-indigo-500', isActive);
                    t.classList.toggle('text-indigo-600', isActive);
                    t.classList.toggle('border-transparent', !isActive);
                    t.classList.toggle('text-slate-500', !isActive);
                });
                pgPanels.forEach(function(p) {
                    p.classList.toggle('hidden', p.id !== 'pg-panel-' + pgId);
                });
            }
            pgTabs.forEach(function(t) {
                t.addEventListener('click', function() {
                    showPg(t.getAttribute('data-pg'));
                });
            });

            /* ── Sandbox / Live toggle ── */
            var modeToggle = document.getElementById('payment-mode-toggle');
            var modeHidden = document.getElementById('payment-mode-hidden');
            var liveWarning = document.getElementById('live-warning');

            function syncMode() {
                var isLive = modeToggle.checked;
                modeHidden.value = isLive ? 'live' : 'sandbox';
                liveWarning.style.display = isLive ? '' : 'none';
            }
            if (modeToggle) {
                modeToggle.addEventListener('change', syncMode);
                syncMode();
            }
        })();
    </script>
@endsection
