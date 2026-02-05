<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $creator->creatorProfile?->headline ?: $creator->name }} ({{ '@' . $creator->username }}) · Creator</title>
    <meta name="description" content="{{ Str::limit($creator->creatorProfile?->tagline ?: $creator->creatorProfile?->bio ?: $creator->name . ' – quizzes and coaching', 160) }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    @php
        $p = $creator->creatorProfile;
        $bio = $creator->bio ?? $p?->bio;
        $headline = $p?->headline;
        $tagline = $p?->tagline;
        $avatarPath = $p?->avatar_path ?? $creator->avatar_path;
        $coverPath = $p?->cover_image_path;
        $galleryImages = $p?->gallery_images ?? [];
        if (!is_array($galleryImages)) { $galleryImages = []; }
        $centerName = $creator->coaching_center_name ?? $p?->coaching_center_name;
        $centerAddress = $p?->coaching_address;
        $centerCity = $creator->coaching_city ?? $p?->coaching_city;
        $centerContact = $creator->coaching_contact ?? $p?->coaching_contact;
        $centerTimings = $p?->coaching_timings;
        $centerWebsite = $creator->coaching_website ?? $p?->coaching_website;
        $coursesOffered = $p?->courses_offered;
        $whatsappNumber = $p?->whatsapp_number;
        $social = $creator->social_links ?? $p?->social_links ?? [];
        if (!is_array($social)) { $social = []; }
        $initials = collect(explode(' ', trim($creator->name)))->filter()->map(fn ($x) => mb_substr($x, 0, 1))->take(2)->implode('');
        if ($initials === '') { $initials = 'C'; }
        $hasCover = $coverPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($coverPath);
        $hasAvatar = $avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
    @endphp

    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-indigo-600/20 to-transparent"></div>
    </div>

    <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-900 text-white font-semibold">Q</div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Creator profile</div>
                </div>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if ($hasCover)
                <div class="h-40 sm:h-52 bg-slate-200">
                    <img src="{{ asset('storage/' . $coverPath) }}" alt="" class="h-full w-full object-cover" />
                </div>
            @else
                <div class="h-40 sm:h-52 bg-gradient-to-br from-indigo-600 to-indigo-800"></div>
            @endif
            <div class="relative px-5 pb-5 sm:px-6 sm:pb-6">
                <div class="-mt-12 sm:-mt-14 flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="shrink-0">
                        @if ($hasAvatar)
                            <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $creator->name }}" class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl border-4 border-white object-cover shadow-lg" />
                        @else
                            <div class="flex h-24 w-24 sm:h-28 sm:w-28 items-center justify-center rounded-2xl border-4 border-white bg-indigo-600 text-3xl font-semibold text-white shadow-lg">{{ $initials }}</div>
                        @endif
                    </div>
                    <div class="min-w-0 pb-1">
                        <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ $headline ?: $creator->name }}</h1>
                        @if ($tagline)
                            <p class="mt-1 text-sm text-slate-600 sm:text-base">{{ $tagline }}</p>
                        @endif
                        <p class="mt-1 text-sm text-slate-500">{{ '@' . $creator->username }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if ($bio)
            <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">About</h2>
                <p class="mt-2 text-slate-700 leading-relaxed whitespace-pre-line">{{ $bio }}</p>
            </section>
        @endif

        {{-- Institute + CTAs --}}
        @if ($centerName || $centerAddress || $centerCity || $centerContact || $centerTimings || $centerWebsite || $coursesOffered || $whatsappNumber)
            <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Institute / Coaching center</h2>
                <p class="mt-1 text-sm text-slate-600">Get in touch or visit.</p>

                <dl class="mt-4 space-y-2 text-sm">
                    @if ($centerName)
                        <div><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-900">{{ $centerName }}</dd></div>
                    @endif
                    @if ($centerAddress)
                        <div><dt class="text-slate-500">Address</dt><dd class="font-medium text-slate-900 whitespace-pre-line">{{ $centerAddress }}</dd></div>
                    @endif
                    @if ($centerCity)
                        <div><dt class="text-slate-500">City</dt><dd class="font-medium text-slate-900">{{ $centerCity }}</dd></div>
                    @endif
                    @if ($centerTimings)
                        <div><dt class="text-slate-500">Timings</dt><dd class="font-medium text-slate-900">{{ $centerTimings }}</dd></div>
                    @endif
                    @if ($centerContact)
                        <div><dt class="text-slate-500">Contact</dt><dd class="font-medium text-slate-900">{{ $centerContact }}</dd></div>
                    @endif
                    @if ($coursesOffered)
                        <div><dt class="text-slate-500">Courses offered</dt><dd class="font-medium text-slate-900 whitespace-pre-line">{{ $coursesOffered }}</dd></div>
                    @endif
                    @if ($centerWebsite)
                        <div><dt class="text-slate-500">Website</dt><dd><a class="font-medium text-indigo-600 hover:underline" href="{{ $centerWebsite }}" target="_blank" rel="noopener noreferrer">{{ $centerWebsite }}</a></dd></div>
                    @endif
                </dl>

                <div class="mt-5 flex flex-wrap gap-3">
                    @if ($whatsappNumber)
                        @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsappNumber); @endphp
                        <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Chat on WhatsApp
                        </a>
                    @endif
                    @if ($centerWebsite)
                        <a href="{{ $centerWebsite }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Visit website</a>
                    @endif
                    @if ($centerContact && !$whatsappNumber)
                        <a href="tel:{{ $centerContact }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Call</a>
                    @endif
                </div>
            </section>
        @endif

        {{-- Gallery --}}
        @if (count($galleryImages) > 0)
            <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Gallery</h2>
                <p class="mt-1 text-sm text-slate-600">Classroom, events &amp; more.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($galleryImages as $path)
                        @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                            <img src="{{ asset('storage/' . $path) }}" alt="" class="aspect-video w-full rounded-xl object-cover border border-slate-200" />
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Social --}}
        @if (!empty($social))
            <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Follow</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($social as $label => $url)
                        @if (is_string($url) && $url !== '')
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">{{ is_string($label) && $label !== '' ? $label : 'Link' }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Quizzes --}}
        <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Public quizzes</h2>
                    <p class="mt-1 text-sm text-slate-600">Practice with {{ $creator->name }}’s quizzes.</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-700">{{ $stats['public_quizzes'] ?? 0 }} quizzes</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-700">{{ $stats['public_questions'] ?? 0 }} questions</span>
                </div>
            </div>

            @if ($publicQuizzes->count() === 0)
                <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                    <p class="text-sm font-semibold text-slate-900">No public quizzes yet</p>
                    <p class="mt-1 text-sm text-slate-600">Check back soon.</p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ($publicQuizzes as $quiz)
                        <a href="{{ route('public.quizzes.show', $quiz->unique_code) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:border-indigo-200 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900">{{ $quiz->title }}</div>
                                    @if ($quiz->description)
                                        <div class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $quiz->description }}</div>
                                    @endif
                                </div>
                                <span class="shrink-0 rounded-xl bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800">{{ strtoupper($quiz->mode) }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                                <span>{{ $quiz->questions_count ?? 0 }} questions</span>
                                <span>Code: {{ $quiz->unique_code }}</span>
                            </div>
                            <div class="mt-3 text-sm font-medium text-indigo-600">Play quiz →</div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6">{{ $publicQuizzes->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
