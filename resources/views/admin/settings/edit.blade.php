@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Settings</h1>
            <p class="mt-1 text-sm text-slate-600">Basic app settings stored in database.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-sm font-medium text-slate-700">Site name</label>
                    <input name="site_name" value="{{ old('site_name', $values['site_name']) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('site_name') border-red-300 @enderror"
                           placeholder="QuizWhiz">
                    @error('site_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Ads enabled</div>
                        <div class="text-xs text-slate-600">Scaffold only (placeholders now, real ads later).</div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="ads_enabled" value="1"
                               class="h-4 w-4 rounded border-slate-300"
                               @checked(old('ads_enabled', $values['ads_enabled']) == '1')>
                        Enabled
                    </label>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="text-sm font-semibold text-slate-900">Ad placements (MVP scaffold)</div>
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
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('ads_interstitial_every_n_results') border-red-300 @enderror">
                        @error('ads_interstitial_every_n_results') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
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

                <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save settings
                </button>
            </form>
        </div>
    </div>
@endsection

