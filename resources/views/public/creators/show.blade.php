<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $creator->creatorProfile?->headline ?: $creator->name }} ({{ '@' . $creator->username }}) · Creator</title>
    <meta name="description" content="{{ Str::limit($creator->creatorProfile?->tagline ?: $creator->creatorProfile?->bio ?: $creator->name . ' – quizzes and coaching', 160) }}">
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-display { font-family: 'DM Serif Display', Georgia, serif; }
        .font-sans { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased font-sans">
    @php
        $p = $creator->creatorProfile;

        // Visibility helper — checks group AND field level
        $v = function(string $group, string $field = null) use ($p) {
            if (!$p) return true;
            // Group must be on
            if (!$p->isVisible($group)) return false;
            // If a specific field is requested, check that too
            if ($field !== null && !$p->isVisible($group . '.' . $field)) return false;
            return true;
        };

        $bio = $p?->bio;
        $headline = $p?->headline;
        $tagline = $p?->tagline;
        $avatarPath = $p?->avatar_path;
        $coverPath = $p?->cover_image_path;
        $galleryImages = $p?->gallery_images ?? [];
        if (!is_array($galleryImages)) { $galleryImages = []; }
        $centerName = $p?->coaching_center_name;
        $centerAddress = $p?->coaching_address;
        $centerCity = $p?->coaching_city;
        $centerContact = $p?->coaching_contact;
        $centerTimings = $p?->coaching_timings;
        $centerWebsite = $p?->coaching_website;
        $coursesOffered = $p?->courses_offered;
        $whatsappNumber = $p?->whatsapp_number;
        $social = $p?->social_links ?? [];
        if (!is_array($social)) { $social = []; }
        $selectedStudents = $p?->selected_students ?? [];
        if (!is_array($selectedStudents)) { $selectedStudents = []; }
        $faculty = $p?->faculty ?? [];
        if (!is_array($faculty)) { $faculty = []; }
        $initials = collect(explode(' ', trim($creator->name)))->filter()->map(fn ($x) => mb_substr($x, 0, 1))->take(2)->implode('');
        if ($initials === '') { $initials = 'C'; }
        $hasCover = $coverPath && $v('images', 'cover') && \Illuminate\Support\Facades\Storage::disk('public')->exists($coverPath);
        $hasAvatar = $avatarPath && $v('images', 'avatar') && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
    @endphp

    {{-- Subtle background texture --}}
    <div class="fixed inset-0 -z-20 bg-slate-100"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(99,102,241,0.12),transparent)]"></div>

    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md supports-[padding:env(safe-area-inset-top)]:pt-[env(safe-area-inset-top)]">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex min-h-[44px] min-w-[44px] items-center gap-2.5 sm:gap-3">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-800 text-white text-sm font-bold shadow-sm sm:h-10 sm:w-10">Q</div>
                <div class="min-w-0 leading-tight">
                    <div class="truncate text-sm font-semibold tracking-tight text-slate-800">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
                    <div class="hidden text-xs text-slate-500 sm:block">Creator profile</div>
                </div>
            </a>
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 pb-12 pt-6 sm:px-6 sm:pt-8 lg:px-8 supports-[padding:env(safe-area-inset-bottom)]:pb-[max(3rem,env(safe-area-inset-bottom))]">
        {{-- Hero card --}}
        <section class="relative overflow-hidden rounded-2xl bg-white shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/60 sm:rounded-3xl">
            @if ($hasCover)
                <div class="relative h-36 sm:h-44 md:h-56">
                    <img src="{{ asset('storage/' . $coverPath) }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-slate-900/20 to-transparent"></div>
                </div>
            @else
                <div class="relative h-36 bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 sm:h-44 md:h-56">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_100%,rgba(255,255,255,0.1),transparent)]"></div>
                </div>
            @endif
            <div class="relative px-5 pb-6 sm:px-8 sm:pb-8">
                <div class="-mt-12 flex flex-col gap-4 sm:-mt-16 sm:flex-row sm:items-end sm:gap-6">
                    <div class="shrink-0">
                        @if ($hasAvatar)
                            <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $creator->name }}" class="h-24 w-24 rounded-2xl border-4 border-white object-cover shadow-xl ring-2 ring-slate-200/80 sm:h-32 sm:w-32 sm:rounded-3xl" loading="lazy" width="128" height="128" />
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-2xl border-4 border-white bg-indigo-600 text-3xl font-bold text-white shadow-xl ring-2 ring-slate-200/80 sm:h-32 sm:w-32 sm:rounded-3xl sm:text-4xl">{{ $initials }}</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1 pb-0.5">
                        @if ($v('about', 'headline') && $headline)
                            <h1 class="font-display text-2xl font-normal tracking-tight text-slate-900 break-words sm:text-3xl md:text-4xl">{{ $headline }}</h1>
                        @else
                            <h1 class="font-display text-2xl font-normal tracking-tight text-slate-900 break-words sm:text-3xl md:text-4xl">{{ $creator->name }}</h1>
                        @endif
                        @if ($v('about', 'tagline') && $tagline)
                            <p class="mt-2 text-sm text-slate-600 break-words sm:text-base">{{ $tagline }}</p>
                        @endif
                        <p class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">&#64;{{ $creator->username }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- About / Bio --}}
        @if ($v('about') && $v('about', 'bio') && $bio)
            <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
                <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">About</h2>
                <div class="mt-1 h-0.5 w-12 rounded-full bg-indigo-500/70"></div>
                <p class="mt-4 max-w-2xl leading-relaxed text-slate-600 whitespace-pre-line break-words text-[15px] sm:text-base">{{ $bio }}</p>
            </section>
        @endif

        {{-- Faculty / Teachers --}}
        @if ($v('faculty') && count($faculty) > 0)
            <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
                <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Faculty &amp; teachers</h2>
                <p class="mt-0.5 text-sm text-slate-500">Meet our team.</p>
                <div class="mt-1 h-0.5 w-12 rounded-full bg-indigo-500/70"></div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
                    @foreach ($faculty as $member)
                        <div class="flex flex-col rounded-xl border border-slate-100 bg-gradient-to-br from-slate-50 to-white p-4 ring-1 ring-slate-100/80 sm:p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg font-bold text-indigo-700">
                                {{ mb_substr($member['name'] ?? '', 0, 1) ?: '?' }}
                            </div>
                            <div class="mt-3 min-w-0 flex-1">
                                <div class="font-semibold text-slate-800">{{ $member['name'] ?? '' }}</div>
                                @if (!empty($member['role']))
                                    <div class="mt-0.5 text-sm font-medium text-indigo-600">{{ $member['role'] }}</div>
                                @endif
                                @if (!empty($member['bio']))
                                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $member['bio'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Institute + CTAs --}}
        @if ($v('institute'))
            @php
                // Build list of visible institute fields
                $instFields = [];
                if ($v('institute','name') && $centerName) $instFields[] = 'name';
                if ($v('institute','address') && $centerAddress) $instFields[] = 'address';
                if ($v('institute','city') && $centerCity) $instFields[] = 'city';
                if ($v('institute','timings') && $centerTimings) $instFields[] = 'timings';
                if ($v('institute','contact') && $centerContact) $instFields[] = 'contact';
                if ($v('institute','whatsapp') && $whatsappNumber) $instFields[] = 'whatsapp';
                if ($v('institute','website') && $centerWebsite) $instFields[] = 'website';
                if ($v('institute','courses') && $coursesOffered) $instFields[] = 'courses';
            @endphp
            @if (count($instFields) > 0)
                <section class="mt-8 overflow-hidden rounded-2xl bg-white shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl">
                    <div class="border-b border-slate-100 bg-slate-50/80 px-6 py-4 sm:px-8">
                        <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Institute &amp; coaching</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Get in touch or visit.</p>
                    </div>
                    <div class="p-6 sm:p-8">
                        <dl class="grid gap-4 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-4">
                            @if (in_array('name', $instFields))
                                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Name</dt><dd class="mt-1 font-medium text-slate-800">{{ $centerName }}</dd></div>
                            @endif
                            @if (in_array('address', $instFields))
                                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Address</dt><dd class="mt-1 font-medium text-slate-800 whitespace-pre-line break-words">{{ $centerAddress }}</dd></div>
                            @endif
                            @if (in_array('city', $instFields))
                                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">City</dt><dd class="mt-1 font-medium text-slate-800">{{ $centerCity }}</dd></div>
                            @endif
                            @if (in_array('timings', $instFields))
                                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Timings</dt><dd class="mt-1 font-medium text-slate-800 break-words">{{ $centerTimings }}</dd></div>
                            @endif
                            @if (in_array('contact', $instFields))
                                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact</dt><dd class="mt-1"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $centerContact) }}" class="font-medium text-indigo-600 hover:text-indigo-700 hover:underline">{{ $centerContact }}</a></dd></div>
                            @endif
                            @if (in_array('courses', $instFields))
                                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Courses offered</dt><dd class="mt-1 font-medium text-slate-800 whitespace-pre-line break-words">{{ $coursesOffered }}</dd></div>
                            @endif
                            @if (in_array('website', $instFields))
                                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Website</dt><dd class="mt-1"><a class="break-all font-medium text-indigo-600 hover:text-indigo-700 hover:underline" href="{{ $centerWebsite }}" target="_blank" rel="noopener noreferrer">{{ $centerWebsite }}</a></dd></div>
                            @endif
                        </dl>

                        <div class="mt-6 flex flex-col gap-3 sm:mt-8 sm:flex-row sm:flex-wrap sm:gap-4">
                            @if (in_array('whatsapp', $instFields))
                                @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsappNumber); @endphp
                                <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-[48px] items-center justify-center gap-2.5 rounded-xl bg-[#25D366] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-green-500/25 transition hover:bg-[#20BD5A] hover:shadow-green-500/30 active:scale-[0.98] sm:min-h-0 sm:py-2.5">
                                    <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Chat on WhatsApp
                                </a>
                            @endif
                            @if (in_array('website', $instFields))
                                <a href="{{ $centerWebsite }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-[48px] items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98] sm:min-h-0 sm:py-2.5">Visit website</a>
                            @endif
                            @if (in_array('contact', $instFields) && !in_array('whatsapp', $instFields))
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $centerContact) }}" class="inline-flex min-h-[48px] items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98] sm:min-h-0 sm:py-2.5">Call</a>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        @endif

        {{-- Achievers --}}
        @if ($v('achievers') && count($selectedStudents) > 0)
            <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
                <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Our achievers</h2>
                <p class="mt-0.5 text-sm text-slate-500">Selected from our institute.</p>
                <div class="mt-1 h-0.5 w-12 rounded-full bg-amber-400/80"></div>
                <ul class="mt-6 grid gap-3 sm:grid-cols-2 sm:gap-4">
                    @foreach ($selectedStudents as $student)
                        <li class="group flex items-center gap-4 rounded-xl border border-slate-100 bg-gradient-to-br from-slate-50 to-white px-4 py-3.5 ring-1 ring-slate-100/80 transition hover:ring-slate-200 sm:px-5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-sm font-bold text-amber-800 group-hover:bg-amber-200/80">✓</span>
                            <div class="min-w-0 flex-1">
                                <span class="font-semibold text-slate-800">{{ $student['name'] ?? '' }}</span>
                                @if (!empty($student['year']) || !empty($student['post']))
                                    <span class="mt-0.5 block text-sm text-slate-500">
                                        @if (!empty($student['year'])){{ $student['year'] }}@endif
                                        @if (!empty($student['year']) && !empty($student['post'])) · @endif
                                        @if (!empty($student['post']))<span class="font-medium text-indigo-600">{{ $student['post'] }}</span>@endif
                                    </span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Gallery --}}
        @if ($v('images') && $v('images', 'gallery') && count($galleryImages) > 0)
            <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
                <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Gallery</h2>
                <p class="mt-0.5 text-sm text-slate-500">Classroom, events &amp; more.</p>
                <div class="mt-1 h-0.5 w-12 rounded-full bg-indigo-500/70"></div>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                    @foreach ($galleryImages as $path)
                        @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                            <div class="overflow-hidden rounded-xl ring-1 ring-slate-200/60 transition hover:ring-slate-300">
                                <img src="{{ asset('storage/' . $path) }}" alt="" class="aspect-video w-full object-cover transition hover:scale-[1.02]" loading="lazy" />
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Social --}}
        @if ($v('social') && !empty($social))
            <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
                <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Follow</h2>
                <div class="mt-1 h-0.5 w-12 rounded-full bg-slate-300"></div>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($social as $label => $url)
                        @if (is_string($url) && $url !== '')
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-100 active:scale-[0.98]">{{ is_string($label) && $label !== '' ? $label : 'Link' }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Quizzes (always visible) --}}
        <section class="mt-8 rounded-2xl bg-white p-6 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/50 sm:rounded-3xl sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-display text-lg font-normal text-slate-900 sm:text-xl">Public quizzes</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Practice with {{ $creator->name }}'s quizzes.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-indigo-100 px-3.5 py-1.5 text-xs font-semibold text-indigo-800">{{ $stats['public_quizzes'] ?? 0 }} quizzes</span>
                    <span class="rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-700">{{ $stats['public_questions'] ?? 0 }} questions</span>
                </div>
            </div>
            <div class="mt-1 h-0.5 w-12 rounded-full bg-indigo-500/70"></div>

            @if ($publicQuizzes->count() === 0)
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center sm:mt-8">
                    <p class="font-medium text-slate-700">No public quizzes yet</p>
                    <p class="mt-1 text-sm text-slate-500">Check back soon.</p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:mt-8 sm:grid-cols-2 sm:gap-5">
                    @foreach ($publicQuizzes as $quiz)
                        <a href="{{ route('public.quizzes.show', $quiz->unique_code) }}" class="group flex flex-col rounded-xl border-2 border-slate-100 bg-slate-50/50 p-5 text-left ring-1 ring-slate-100 transition hover:border-indigo-200 hover:bg-white hover:shadow-lg hover:shadow-indigo-500/5 hover:ring-indigo-100 active:scale-[0.99] sm:rounded-2xl">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-slate-800 break-words group-hover:text-indigo-700">{{ $quiz->title }}</div>
                                    @if ($quiz->description)
                                        <div class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $quiz->description }}</div>
                                    @endif
                                </div>
                                <span class="w-fit shrink-0 rounded-lg bg-indigo-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-indigo-800">{{ $quiz->mode }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span>{{ $quiz->questions_count ?? 0 }} questions</span>
                                <span class="text-slate-300">·</span>
                                <span class="font-mono text-slate-600">{{ $quiz->unique_code }}</span>
                            </div>
                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 group-hover:text-indigo-700">
                                <span>Play quiz</span>
                                <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6 sm:mt-8">{{ $publicQuizzes->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
