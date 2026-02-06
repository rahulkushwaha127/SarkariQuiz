@extends('layouts.creator')

@section('title', 'My profile / Bio page')

@section('content')
@php
    $vis = $profile->section_visibility ?? [];
    // helper: returns checked state for a visibility key (default on)
    $isOn = function(string $key) use ($vis) {
        return (bool) ($vis[$key] ?? true);
    };
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My profile &amp; bio page</h1>
        <p class="mt-1 text-sm text-slate-600">Manage your public page. Toggle sections and individual fields on/off — disabled items won't show on your bio page.</p>
        @if($user->username)
            <p class="mt-2">
                <a href="{{ route('public.creators.show', $user->username) }}" target="_blank" rel="noopener"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">View my public page →</a>
                <span class="ml-2 text-slate-500">/c/{{ $user->username }}</span>
            </p>
        @else
            <p class="mt-2 text-amber-700 text-sm">Set a <strong>username</strong> below and save to get your public page.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Username — always visible, no toggle --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Public page URL</h2>
            <p class="mt-1 text-xs text-slate-500">Your page will be at <strong>/c/username</strong>. Letters, numbers, hyphens only.</p>
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                       placeholder="e.g. my-coaching" pattern="[a-zA-Z0-9_-]+"
                       class="mt-1 w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            {{-- Tab navigation --}}
            <div class="border-b border-slate-200 bg-slate-50/60">
                <nav class="flex overflow-x-auto -mb-px" id="profile-tabs">
                    <button type="button" data-tab="about"     class="profile-tab whitespace-nowrap border-b-2 border-indigo-600 px-5 py-3 text-sm font-semibold text-indigo-700">About</button>
                    <button type="button" data-tab="institute" class="profile-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Institute</button>
                    <button type="button" data-tab="images"    class="profile-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Images</button>
                    <button type="button" data-tab="faculty"   class="profile-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Faculty</button>
                    <button type="button" data-tab="achievers" class="profile-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Achievers</button>
                    <button type="button" data-tab="social"    class="profile-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Social</button>
                </nav>
            </div>

            {{-- ==================== TAB: About ==================== --}}
            <div class="tab-panel p-5 sm:p-6" data-panel="about">
                {{-- Group toggle --}}
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">About you</h3>
                        <p class="text-xs text-slate-500">Headline, tagline & bio shown at the top of your page.</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[about]', 'checked' => $isOn('about')])
                </div>

                <div class="mt-5 space-y-5">
                    {{-- Headline --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Headline</label>
                            <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}"
                                   placeholder="e.g. UPSC &amp; SSC Coaching in Delhi"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[about.headline]', 'checked' => $isOn('about.headline'), 'small' => true])
                    </div>
                    {{-- Tagline --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Tagline (one line)</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}"
                                   placeholder="e.g. Trusted by 10,000+ aspirants"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[about.tagline]', 'checked' => $isOn('about.tagline'), 'small' => true])
                    </div>
                    {{-- Bio --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Bio</label>
                            <textarea name="bio" rows="4" placeholder="Short intro about you and your coaching..."
                                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('bio', $profile->bio) }}</textarea>
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[about.bio]', 'checked' => $isOn('about.bio'), 'small' => true])
                    </div>
                </div>
            </div>

            {{-- ==================== TAB: Institute ==================== --}}
            <div class="tab-panel hidden p-5 sm:p-6" data-panel="institute">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Institute / Coaching center</h3>
                        <p class="text-xs text-slate-500">Let students find your center and contact you.</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[institute]', 'checked' => $isOn('institute')])
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    {{-- Name --}}
                    <div class="sm:col-span-2 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Institute name</label>
                            <input type="text" name="coaching_center_name" value="{{ old('coaching_center_name', $profile->coaching_center_name) }}"
                                   placeholder="e.g. ABC Academy" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.name]', 'checked' => $isOn('institute.name'), 'small' => true])
                    </div>
                    {{-- Address --}}
                    <div class="sm:col-span-2 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Full address</label>
                            <textarea name="coaching_address" rows="2" placeholder="Building, area, city, state, PIN"
                                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('coaching_address', $profile->coaching_address) }}</textarea>
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.address]', 'checked' => $isOn('institute.address'), 'small' => true])
                    </div>
                    {{-- City --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">City</label>
                            <input type="text" name="coaching_city" value="{{ old('coaching_city', $profile->coaching_city) }}"
                                   placeholder="e.g. Delhi" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.city]', 'checked' => $isOn('institute.city'), 'small' => true])
                    </div>
                    {{-- Timings --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Timings</label>
                            <input type="text" name="coaching_timings" value="{{ old('coaching_timings', $profile->coaching_timings) }}"
                                   placeholder="e.g. Mon–Sat 9 AM–6 PM" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.timings]', 'checked' => $isOn('institute.timings'), 'small' => true])
                    </div>
                    {{-- Phone --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Phone / Contact</label>
                            <input type="text" name="coaching_contact" value="{{ old('coaching_contact', $profile->coaching_contact) }}"
                                   placeholder="e.g. +91 98765 43210" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.contact]', 'checked' => $isOn('institute.contact'), 'small' => true])
                    </div>
                    {{-- WhatsApp --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">WhatsApp number</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}"
                                   placeholder="e.g. 919876543210" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">With country code, no +. Used for &quot;Chat on WhatsApp&quot; button.</p>
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.whatsapp]', 'checked' => $isOn('institute.whatsapp'), 'small' => true])
                    </div>
                    {{-- Website --}}
                    <div class="sm:col-span-2 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Website</label>
                            <input type="url" name="coaching_website" value="{{ old('coaching_website', $profile->coaching_website) }}"
                                   placeholder="https://..." class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.website]', 'checked' => $isOn('institute.website'), 'small' => true])
                    </div>
                    {{-- Courses --}}
                    <div class="sm:col-span-2 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Courses offered</label>
                            <textarea name="courses_offered" rows="3" placeholder="e.g. UPSC Prelims, SSC CGL, State PSC"
                                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('courses_offered', $profile->courses_offered) }}</textarea>
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[institute.courses]', 'checked' => $isOn('institute.courses'), 'small' => true])
                    </div>
                </div>
            </div>

            {{-- ==================== TAB: Images ==================== --}}
            <div class="tab-panel hidden p-5 sm:p-6" data-panel="images">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Images</h3>
                        <p class="text-xs text-slate-500">Profile photo, cover banner & gallery (max 6). JPG, PNG, WebP up to 2 MB each.</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[images]', 'checked' => $isOn('images')])
                </div>

                <div class="mt-5 space-y-6">
                    {{-- Avatar --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Profile photo</label>
                            @if($profile->avatar_path)
                                <div class="mt-2 flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $profile->avatar_path) }}" alt="Avatar" class="h-20 w-20 rounded-2xl object-cover border border-slate-200" />
                                    <label class="cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="sr-only" /> Change
                                    </label>
                                    <label class="inline-flex items-center gap-1 text-sm text-slate-600">
                                        <input type="checkbox" name="remove_avatar" value="1" /> Remove
                                    </label>
                                </div>
                            @else
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                                       class="mt-2 block w-full max-w-xs text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700" />
                            @endif
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[images.avatar]', 'checked' => $isOn('images.avatar'), 'small' => true])
                    </div>
                    {{-- Cover --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Cover / banner image</label>
                            <p class="mt-1 text-xs text-slate-500">Wide image at the top (e.g. 1200×400).</p>
                            @if($profile->cover_image_path)
                                <div class="mt-2 flex items-start gap-3">
                                    <img src="{{ asset('storage/' . $profile->cover_image_path) }}" alt="Cover" class="max-h-24 rounded-xl object-cover border border-slate-200" />
                                    <div class="flex flex-col gap-2">
                                        <label class="cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="sr-only" /> Change
                                        </label>
                                        <label class="inline-flex items-center gap-1 text-sm text-slate-600">
                                            <input type="checkbox" name="remove_cover_image" value="1" /> Remove cover
                                        </label>
                                    </div>
                                </div>
                            @else
                                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                                       class="mt-2 block w-full max-w-xs text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700" />
                            @endif
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[images.cover]', 'checked' => $isOn('images.cover'), 'small' => true])
                    </div>
                    {{-- Gallery --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-sm font-medium text-slate-700">Gallery (classroom, events, etc.)</label>
                            <p class="mt-1 text-xs text-slate-500">Up to 6 images. Tick &quot;Remove&quot; to delete one.</p>
                            @if(!empty($profile->gallery_images))
                                <div class="mt-3 flex flex-wrap gap-3">
                                    @foreach($profile->gallery_images as $idx => $path)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $path) }}" alt="Gallery {{ $idx + 1 }}" class="h-24 w-32 rounded-xl object-cover border border-slate-200" />
                                            <label class="absolute -top-2 -right-2 inline-flex items-center gap-1 rounded bg-red-100 px-2 py-0.5 text-xs text-red-800">
                                                <input type="checkbox" name="remove_gallery_index[]" value="{{ $idx }}" /> Remove
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple
                                   class="mt-3 block w-full max-w-xs text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700" />
                        </div>
                        @include('creator.profile._toggle', ['name' => 'visibility[images.gallery]', 'checked' => $isOn('images.gallery'), 'small' => true])
                    </div>
                </div>
            </div>

            {{-- ==================== TAB: Faculty ==================== --}}
            <div class="tab-panel hidden p-5 sm:p-6" data-panel="faculty">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Faculty / Teachers</h3>
                        <p class="text-xs text-slate-500">Add faculty from your institute. Shown on your public bio page.</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[faculty]', 'checked' => $isOn('faculty')])
                </div>

                @php
                    $facultyRows = old('faculty', $profile->faculty ?? []);
                    if (!is_array($facultyRows)) { $facultyRows = []; }
                    if (empty($facultyRows)) { $facultyRows = [['name' => '', 'role' => '', 'bio' => '']]; }
                @endphp
                <div id="faculty-wrap" class="mt-5 space-y-3">
                    @foreach($facultyRows as $i => $row)
                        <div class="faculty-row flex flex-wrap gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-3 sm:gap-3">
                            <input type="text" name="faculty[{{ $i }}][name]" value="{{ old('faculty.'.$i.'.name', $row['name'] ?? '') }}"
                                   placeholder="Name" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />
                            <input type="text" name="faculty[{{ $i }}][role]" value="{{ old('faculty.'.$i.'.role', $row['role'] ?? '') }}"
                                   placeholder="Role (e.g. Mathematics)" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />
                            <input type="text" name="faculty[{{ $i }}][bio]" value="{{ old('faculty.'.$i.'.bio', $row['bio'] ?? '') }}"
                                   placeholder="Short bio (optional)" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[180px]" />
                            <button type="button" class="remove-faculty rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Remove</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-faculty" class="mt-3 inline-flex items-center rounded-xl border border-dashed border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">+ Add faculty</button>
            </div>

            {{-- ==================== TAB: Achievers ==================== --}}
            <div class="tab-panel hidden p-5 sm:p-6" data-panel="achievers">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Selected students / Achievers</h3>
                        <p class="text-xs text-slate-500">Showcase students who got selected (e.g. IAS, SSC CGL).</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[achievers]', 'checked' => $isOn('achievers')])
                </div>

                @php
                    $students = old('selected_students', $profile->selected_students ?? []);
                    if (!is_array($students)) { $students = []; }
                    if (empty($students)) { $students = [['name' => '', 'year' => '', 'post' => '']]; }
                @endphp
                <div id="selected-students-wrap" class="mt-5 space-y-3">
                    @foreach($students as $i => $row)
                        <div class="selected-student-row flex flex-wrap items-center gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-3 sm:gap-3">
                            <input type="text" name="selected_students[{{ $i }}][name]" value="{{ old('selected_students.'.$i.'.name', $row['name'] ?? '') }}"
                                   placeholder="Student name" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />
                            <input type="text" name="selected_students[{{ $i }}][year]" value="{{ old('selected_students.'.$i.'.year', $row['year'] ?? '') }}"
                                   placeholder="Year (e.g. 2024)" class="w-24 rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            <input type="text" name="selected_students[{{ $i }}][post]" value="{{ old('selected_students.'.$i.'.post', $row['post'] ?? '') }}"
                                   placeholder="Post (e.g. IAS, SSC CGL)" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[120px]" />
                            <button type="button" class="remove-student rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Remove</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-selected-student" class="mt-3 inline-flex items-center rounded-xl border border-dashed border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">+ Add student</button>
            </div>

            {{-- ==================== TAB: Social ==================== --}}
            <div class="tab-panel hidden p-5 sm:p-6" data-panel="social">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Social links</h3>
                        <p class="text-xs text-slate-500">YouTube, Instagram, Telegram, etc.</p>
                    </div>
                    @include('creator.profile._toggle', ['name' => 'visibility[social]', 'checked' => $isOn('social')])
                </div>

                @php
                    $social = $profile->social_links ?? [];
                    if (!is_array($social)) { $social = []; }
                    $socialEntries = empty($social) ? [['label' => '', 'url' => '']] : collect($social)->map(fn ($url, $label) => ['label' => $label, 'url' => $url])->values()->all();
                @endphp
                <div id="social-links-wrap" class="mt-5 space-y-3">
                    @foreach($socialEntries as $i => $entry)
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            <input type="text" name="social_labels[{{ $i }}]" value="{{ old('social_labels.'.$i, $entry['label']) }}"
                                   placeholder="Label (e.g. YouTube)" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            <input type="url" name="social_urls[{{ $i }}]" value="{{ old('social_urls.'.$i, $entry['url']) }}"
                                   placeholder="https://..." class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-500">Leave both fields empty to ignore a row.</p>
                <div class="mt-2 flex flex-wrap gap-2 sm:gap-3">
                    <input type="text" name="social_labels[{{ count($socialEntries) }}]" placeholder="Label"
                           class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    <input type="url" name="social_urls[{{ count($socialEntries) }}]" placeholder="https://..."
                           class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                </div>
            </div>
        </div>

        {{-- Save --}}
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save profile</button>
            <a href="{{ route('creator.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    /* ---- Tabs ---- */
    var tabs = document.querySelectorAll('.profile-tab');
    var panels = document.querySelectorAll('.tab-panel');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            tabs.forEach(function(t) {
                t.classList.remove('border-indigo-600', 'text-indigo-700', 'font-semibold');
                t.classList.add('border-transparent', 'text-slate-500', 'font-medium');
            });
            this.classList.add('border-indigo-600', 'text-indigo-700', 'font-semibold');
            this.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
            panels.forEach(function(p) {
                p.classList.toggle('hidden', p.getAttribute('data-panel') !== target);
            });
        });
    });

    /* ---- Group toggle: dim body when group is off ---- */
    document.querySelectorAll('.vis-toggle').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var panel = this.closest('.tab-panel');
            if (!panel) return;
            // Only group toggles (not .vis-toggle-sm) affect the panel
            if (this.classList.contains('vis-toggle-sm')) return;
            var fields = panel.querySelectorAll('.vis-toggle-sm');
            // When group is off, uncheck all field toggles and disable
            fields.forEach(function(f) {
                if (!cb.checked) {
                    f.checked = false;
                    f.closest('label').classList.add('opacity-40', 'pointer-events-none');
                } else {
                    f.closest('label').classList.remove('opacity-40', 'pointer-events-none');
                }
            });
        });
        // trigger on load
        if (!cb.classList.contains('vis-toggle-sm')) {
            cb.dispatchEvent(new Event('change'));
        }
    });

    /* ---- Faculty add/remove ---- */
    var facultyWrap = document.getElementById('faculty-wrap');
    var addFacultyBtn = document.getElementById('add-faculty');
    if (facultyWrap && addFacultyBtn) {
        addFacultyBtn.addEventListener('click', function() {
            var n = facultyWrap.querySelectorAll('.faculty-row').length;
            var row = document.createElement('div');
            row.className = 'faculty-row flex flex-wrap gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-3 sm:gap-3';
            row.innerHTML =
                '<input type="text" name="faculty['+n+'][name]" placeholder="Name" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />' +
                '<input type="text" name="faculty['+n+'][role]" placeholder="Role" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />' +
                '<input type="text" name="faculty['+n+'][bio]" placeholder="Short bio (optional)" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[180px]" />' +
                '<button type="button" class="remove-faculty rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Remove</button>';
            facultyWrap.appendChild(row);
            row.querySelector('.remove-faculty').addEventListener('click', function() { row.remove(); });
        });
        facultyWrap.querySelectorAll('.remove-faculty').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.faculty-row').remove(); });
        });
    }

    /* ---- Selected students add/remove ---- */
    var sWrap = document.getElementById('selected-students-wrap');
    var addS = document.getElementById('add-selected-student');
    if (sWrap && addS) {
        addS.addEventListener('click', function() {
            var n = sWrap.querySelectorAll('.selected-student-row').length;
            var row = document.createElement('div');
            row.className = 'selected-student-row flex flex-wrap items-center gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-3 sm:gap-3';
            row.innerHTML =
                '<input type="text" name="selected_students['+n+'][name]" placeholder="Student name" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[140px]" />' +
                '<input type="text" name="selected_students['+n+'][year]" placeholder="Year" class="w-24 rounded-lg border border-slate-200 px-3 py-2 text-sm" />' +
                '<input type="text" name="selected_students['+n+'][post]" placeholder="Post" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm sm:min-w-[120px]" />' +
                '<button type="button" class="remove-student rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Remove</button>';
            sWrap.appendChild(row);
            row.querySelector('.remove-student').addEventListener('click', function() { row.remove(); });
        });
        sWrap.querySelectorAll('.remove-student').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.selected-student-row').remove(); });
        });
    }
})();
</script>
@endpush
@endsection
