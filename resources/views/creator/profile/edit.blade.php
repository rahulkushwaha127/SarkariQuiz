@extends('layouts.creator')

@section('title', 'My profile / Bio page')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My profile &amp; bio page</h1>
        <p class="mt-1 text-sm text-slate-600">This is your public page where students see your institute and quizzes. Add a headline, images, and coaching details to advertise your institute.</p>
        @if($user->username)
            <p class="mt-2">
                <a href="{{ route('public.creators.show', $user->username) }}" target="_blank" rel="noopener"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">View my public page →</a>
                <span class="ml-2 text-slate-500">/c/{{ $user->username }}</span>
            </p>
        @else
            <p class="mt-2 text-amber-700 text-sm">Set a <strong>username</strong> below and save to get your public page. Then the link will appear here.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Username (for public page URL) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Public page URL</h2>
            <p class="mt-1 text-xs text-slate-500">Your page will be at <strong>/c/username</strong>. Use letters, numbers, and hyphens only.</p>
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                       placeholder="e.g. my-coaching"
                       pattern="[a-zA-Z0-9_-]+"
                       class="mt-1 w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- About you --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">About you</h2>
            <p class="mt-1 text-xs text-slate-500">Shown at the top of your public page.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Headline</label>
                    <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}"
                           placeholder="e.g. UPSC &amp; SSC Coaching in Delhi"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tagline (one line)</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}"
                           placeholder="e.g. Trusted by 10,000+ aspirants"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Bio</label>
                    <textarea name="bio" rows="4" placeholder="Short intro about you and your coaching..."
                              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('bio', $profile->bio) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Institute / Coaching --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Institute / Coaching center</h2>
            <p class="mt-1 text-xs text-slate-500">Let students find your center and contact you.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Institute name</label>
                    <input type="text" name="coaching_center_name" value="{{ old('coaching_center_name', $profile->coaching_center_name) }}"
                           placeholder="e.g. ABC Academy"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Full address</label>
                    <textarea name="coaching_address" rows="2" placeholder="Building, area, city, state, PIN"
                              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('coaching_address', $profile->coaching_address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">City</label>
                    <input type="text" name="coaching_city" value="{{ old('coaching_city', $profile->coaching_city) }}"
                           placeholder="e.g. Delhi"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Timings</label>
                    <input type="text" name="coaching_timings" value="{{ old('coaching_timings', $profile->coaching_timings) }}"
                           placeholder="e.g. Mon–Sat 9 AM–6 PM"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Phone / Contact</label>
                    <input type="text" name="coaching_contact" value="{{ old('coaching_contact', $profile->coaching_contact) }}"
                           placeholder="e.g. +91 98765 43210"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">WhatsApp number</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}"
                           placeholder="e.g. 919876543210 (with country code, no +)"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    <p class="mt-1 text-xs text-slate-500">Used for a &quot;Chat on WhatsApp&quot; button on your page.</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Website</label>
                    <input type="url" name="coaching_website" value="{{ old('coaching_website', $profile->coaching_website) }}"
                           placeholder="https://..."
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Courses offered</label>
                    <textarea name="courses_offered" rows="3" placeholder="e.g. UPSC Prelims, SSC CGL, State PSC, etc."
                              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('courses_offered', $profile->courses_offered) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Images --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Images</h2>
            <p class="mt-1 text-xs text-slate-500">Profile photo, cover banner, and gallery (max 6). JPG, PNG or WebP, up to 2 MB each.</p>
            <div class="mt-4 space-y-6">
                <div class="flex flex-wrap items-start gap-4">
                    <div>
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
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Cover / banner image</label>
                    <p class="mt-1 text-xs text-slate-500">Wide image at the top of your page (e.g. 1200×400).</p>
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
                <div>
                    <label class="block text-sm font-medium text-slate-700">Gallery (classroom, events, etc.)</label>
                    <p class="mt-1 text-xs text-slate-500">Up to 6 images. New uploads are added; tick &quot;Remove&quot; to delete one.</p>
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
            </div>
        </div>

        {{-- Social links --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Social links</h2>
            <p class="mt-1 text-xs text-slate-500">YouTube, Instagram, Telegram, etc. Label and URL for each.</p>
            @php
                $social = $profile->social_links ?? [];
                if (!is_array($social)) { $social = []; }
                $socialEntries = empty($social) ? [['label' => '', 'url' => '']] : collect($social)->map(fn ($url, $label) => ['label' => $label, 'url' => $url])->values()->all();
            @endphp
            <div id="social-links-wrap" class="mt-4 space-y-3">
                @foreach($socialEntries as $i => $entry)
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        <input type="text" name="social_labels[{{ $i }}]" value="{{ old('social_labels.'.$i, $entry['label']) }}"
                               placeholder="Label (e.g. YouTube)"
                               class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                        <input type="url" name="social_urls[{{ $i }}]" value="{{ old('social_urls.'.$i, $entry['url']) }}"
                               placeholder="https://..."
                               class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-slate-500">Add more: leave one row with both fields empty and we’ll ignore it; or add a single new row below if you need.</p>
            <div class="mt-2 flex flex-wrap gap-2 sm:gap-3">
                <input type="text" name="social_labels[{{ count($socialEntries) }}]" placeholder="Label"
                       class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                <input type="url" name="social_urls[{{ count($socialEntries) }}]" placeholder="https://..."
                       class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Save profile
            </button>
            <a href="{{ route('creator.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
