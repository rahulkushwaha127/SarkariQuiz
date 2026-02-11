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
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="captcha">
                    CAPTCHA
                </button>
                <button type="button"
                        class="settings-tab border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700"
                        data-tab="pwa">
                    PWA
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
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
                @php
                    $menuLabels = [
                        'home' => 'Home',
                        'exams' => 'Exams',
                        'practice' => 'Practice',
                        'pyq' => 'PYQ Bank',
                        'revision' => 'Revision',
                        'clubs' => 'Clubs',
                        'notifications' => 'Notifications',
                        'public_contests' => 'Public Contests',
                        'join_contest' => 'Join Contest',
                        'daily_challenge' => 'Daily Challenge',
                        'leaderboard' => 'Leaderboard',
                        'batches' => 'My Batches',
                        'subscription' => 'Plans',
                        'profile' => 'My Profile',
                    ];
                @endphp
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach(array_keys($values['frontend_menu']) as $key)
                        @php
                            $label = $menuLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
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

            {{-- Tab: CAPTCHA --}}
            @php
                $cv = $values['captcha'] ?? [];
            @endphp
            <div id="panel-captcha" class="settings-panel hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">CAPTCHA (reCAPTCHA v2)</h2>
                <p class="mt-1 text-sm text-slate-600">Protect login, register, and contact forms. Get keys at <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Google reCAPTCHA</a>.</p>

                <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">CAPTCHA enabled</div>
                        <div class="text-xs text-slate-600">Show reCAPTCHA on login, register, and contact forms.</div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="captcha_enabled" value="1"
                               class="h-4 w-4 rounded border-slate-300"
                               @checked(old('captcha_enabled', $cv['captcha_enabled'] ?? '0') == '1')>
                        Enabled
                    </label>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Site key (public)</label>
                        <input type="text" name="captcha_site_key"
                               value="{{ old('captcha_site_key', $cv['captcha_site_key'] ?? '') }}"
                               placeholder="6Lc..."
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('captcha_site_key') border-red-300 @enderror">
                        @error('captcha_site_key') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secret key</label>
                        <input type="password" name="captcha_secret_key"
                               value=""
                               placeholder="{{ ($cv['captcha_secret_key'] ?? '') !== '' ? 'Leave blank to keep current' : 'Enter secret' }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                        <p class="mt-1 text-xs text-slate-500">Leave blank to keep existing secret.</p>
                    </div>
                </div>
            </div>

            {{-- Tab: PWA --}}
            @php
                $pwa = $values['pwa'] ?? [];
            @endphp
            <div id="panel-pwa" class="settings-panel hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">PWA (Progressive Web App)</h2>
                <p class="mt-1 text-sm text-slate-600">Configure manifest and meta for "Add to Home Screen" and install prompt. Upload PNG icons (192×192 and 512×512).</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">App name</label>
                        <input type="text" name="pwa_name" value="{{ old('pwa_name', $pwa['pwa_name'] ?? '') }}"
                               placeholder="QuizWhiz"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('pwa_name') border-red-300 @enderror">
                        @error('pwa_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">Full name shown in install dialog and splash.</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Short name</label>
                        <input type="text" name="pwa_short_name" value="{{ old('pwa_short_name', $pwa['pwa_short_name'] ?? '') }}"
                               placeholder="QuizWhiz" maxlength="50"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('pwa_short_name') border-red-300 @enderror">
                        @error('pwa_short_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">Label under home screen icon (keep short).</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700">Start URL</label>
                    <input type="text" name="pwa_start_url" value="{{ old('pwa_start_url', $pwa['pwa_start_url'] ?? '/') }}"
                           placeholder="/"
                           class="mt-1 w-full max-w-md rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('pwa_start_url') border-red-300 @enderror">
                    @error('pwa_start_url') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    <p class="mt-1 text-xs text-slate-500">Path when user opens the app from home screen (e.g. <code>/</code> or <code>/dashboard</code>).</p>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Theme color</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" name="pwa_theme_color" value="{{ old('pwa_theme_color', $pwa['pwa_theme_color'] ?? '#4f46e5') }}"
                                   class="h-10 w-14 cursor-pointer rounded-lg border border-slate-200 bg-white p-1 @error('pwa_theme_color') border-red-300 @enderror">
                            <span class="text-sm text-slate-600" id="pwa-theme-color-hex">{{ old('pwa_theme_color', $pwa['pwa_theme_color'] ?? '#4f46e5') }}</span>
                        </div>
                        @error('pwa_theme_color') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">Status bar / browser chrome.</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Background color</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" name="pwa_background_color" value="{{ old('pwa_background_color', $pwa['pwa_background_color'] ?? '#ffffff') }}"
                                   class="h-10 w-14 cursor-pointer rounded-lg border border-slate-200 bg-white p-1 @error('pwa_background_color') border-red-300 @enderror">
                            <span class="text-sm text-slate-600" id="pwa-bg-color-hex">{{ old('pwa_background_color', $pwa['pwa_background_color'] ?? '#ffffff') }}</span>
                        </div>
                        @error('pwa_background_color') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">Splash screen and background.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700">Display</label>
                    <select name="pwa_display"
                            class="mt-1 w-full max-w-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                        <option value="standalone" @selected(old('pwa_display', $pwa['pwa_display'] ?? 'standalone') === 'standalone')>Standalone (app-like, no browser UI)</option>
                        <option value="fullscreen" @selected(old('pwa_display', $pwa['pwa_display'] ?? '') === 'fullscreen')>Fullscreen</option>
                        <option value="minimal-ui" @selected(old('pwa_display', $pwa['pwa_display'] ?? '') === 'minimal-ui')>Minimal UI</option>
                        <option value="browser" @selected(old('pwa_display', $pwa['pwa_display'] ?? '') === 'browser')>Browser</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">How the app appears when launched from home screen.</p>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Icon 192×192</label>
                        @if(!empty($pwa['pwa_icon_192']))
                            <div class="mt-1 flex items-center gap-3">
                                <img src="{{ asset($pwa['pwa_icon_192']) }}" alt="192" class="h-12 w-12 rounded-lg border border-slate-200 object-cover">
                                <span class="text-xs text-slate-500">Current icon. Upload new to replace.</span>
                            </div>
                        @endif
                        <input type="file" name="pwa_icon_192" accept="image/png"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 focus:border-slate-400 focus:outline-none @error('pwa_icon_192') border-red-300 @enderror">
                        @error('pwa_icon_192') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">PNG, 192×192 px. Leave empty to keep current.</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Icon 512×512</label>
                        @if(!empty($pwa['pwa_icon_512']))
                            <div class="mt-1 flex items-center gap-3">
                                <img src="{{ asset($pwa['pwa_icon_512']) }}" alt="512" class="h-12 w-12 rounded-lg border border-slate-200 object-cover">
                                <span class="text-xs text-slate-500">Current icon. Upload new to replace.</span>
                            </div>
                        @endif
                        <input type="file" name="pwa_icon_512" accept="image/png"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 focus:border-slate-400 focus:outline-none @error('pwa_icon_512') border-red-300 @enderror">
                        @error('pwa_icon_512') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-1 text-xs text-slate-500">PNG, 512×512 px (splash). Leave empty to keep current.</p>
                    </div>
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
                            <input type="hidden" name="payment_mode" value="sandbox" id="payment-mode-hidden">
                            <div class="mt-2 inline-flex overflow-hidden rounded-xl border border-slate-200 bg-slate-100" id="mode-toggle-wrap">
                                <button type="button" id="mode-btn-sandbox"
                                        class="mode-toggle-btn px-5 py-2.5 text-sm font-semibold transition-all {{ $paymentMode !== 'live' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">
                                    Sandbox
                                </button>
                                <button type="button" id="mode-btn-live"
                                        class="mode-toggle-btn px-5 py-2.5 text-sm font-semibold transition-all {{ $paymentMode === 'live' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">
                                    Live
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-amber-600" id="live-warning" style="{{ $paymentMode === 'live' ? '' : 'display:none' }}">
                                <svg class="mr-1 inline h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
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

            /* ── Sandbox / Live toggle buttons ── */
            var modeHidden = document.getElementById('payment-mode-hidden');
            var liveWarning = document.getElementById('live-warning');
            var btnSandbox = document.getElementById('mode-btn-sandbox');
            var btnLive = document.getElementById('mode-btn-live');

            function setMode(mode) {
                modeHidden.value = mode;
                var isLive = mode === 'live';
                liveWarning.style.display = isLive ? '' : 'none';

                // Sandbox button
                btnSandbox.className = 'mode-toggle-btn px-5 py-2.5 text-sm font-semibold transition-all '
                    + (!isLive ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700');

                // Live button
                btnLive.className = 'mode-toggle-btn px-5 py-2.5 text-sm font-semibold transition-all '
                    + (isLive ? 'bg-emerald-600 text-white shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700');
            }
            if (btnSandbox && btnLive) {
                btnSandbox.addEventListener('click', function() { setMode('sandbox'); });
                btnLive.addEventListener('click', function() { setMode('live'); });
            }

            /* ── PWA color inputs: show hex next to picker ── */
            var themeColorInput = document.querySelector('input[name="pwa_theme_color"]');
            var themeColorHex = document.getElementById('pwa-theme-color-hex');
            var bgColorInput = document.querySelector('input[name="pwa_background_color"]');
            var bgColorHex = document.getElementById('pwa-bg-color-hex');
            if (themeColorInput && themeColorHex) {
                themeColorInput.addEventListener('input', function() { themeColorHex.textContent = this.value; });
            }
            if (bgColorInput && bgColorHex) {
                bgColorInput.addEventListener('input', function() { bgColorHex.textContent = this.value; });
            }
        })();
    </script>
@endsection
