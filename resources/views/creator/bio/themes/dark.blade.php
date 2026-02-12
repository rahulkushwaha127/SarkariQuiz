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
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-display { font-family: 'Space Grotesk', sans-serif; }
        .font-body    { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        html { scroll-behavior: smooth; }
        .fade-up { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .fade-up.is-visible { opacity: 1; transform: translateY(0); }
        .hero-bg { background-size: cover; background-position: center; background-attachment: scroll; }
        @media (min-width: 768px) { .hero-bg { background-attachment: fixed; } }
        .text-gradient { background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hover-lift { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .section-divider { width: 48px; height: 3px; border-radius: 3px; background: linear-gradient(90deg, #34d399, #10b981); }
        .gallery-item img { transition: transform 0.4s ease; }
        .gallery-item:hover img { transform: scale(1.05); }
        .dark-card { background: rgba(39, 39, 42, 0.8); border: 1px solid rgba(63, 63, 70, 0.8); }
    </style>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased font-body">
    @php
        $p = $creator->creatorProfile;

        // Visibility helper
        $v = function(string $group, string $field = null) use ($p) {
            if (!$p) return true;
            if (!$p->isVisible($group)) return false;
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

        // Institute fields
        $instFields = [];
        if ($v('institute')) {
            if ($v('institute','name') && $centerName) $instFields[] = 'name';
            if ($v('institute','address') && $centerAddress) $instFields[] = 'address';
            if ($v('institute','city') && $centerCity) $instFields[] = 'city';
            if ($v('institute','timings') && $centerTimings) $instFields[] = 'timings';
            if ($v('institute','contact') && $centerContact) $instFields[] = 'contact';
            if ($v('institute','whatsapp') && $whatsappNumber) $instFields[] = 'whatsapp';
            if ($v('institute','website') && $centerWebsite) $instFields[] = 'website';
            if ($v('institute','courses') && $coursesOffered) $instFields[] = 'courses';
        }

        $hasWhatsapp = in_array('whatsapp', $instFields);
        $hasContact  = in_array('contact', $instFields);
        $hasWebsite  = in_array('website', $instFields);
        $waLink = $hasWhatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsappNumber) : '#';
    @endphp

    {{-- Dark theme: sticky navbar --}}
    <header class="sticky top-0 z-50 border-b border-zinc-800 bg-zinc-950/95 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-3 sm:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-500/20">Q</div>
                <span class="text-sm font-semibold tracking-tight text-zinc-100">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</span>
            </a>
            <nav class="flex items-center gap-3">
                @if($hasWhatsapp)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="hidden items-center gap-2 rounded-full bg-[#25D366] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#20BD5A] sm:inline-flex">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                @endif
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full border border-zinc-600 bg-zinc-800 px-4 py-2 text-xs font-semibold text-zinc-200 transition hover:bg-zinc-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-zinc-600 bg-zinc-800 px-4 py-2 text-xs font-semibold text-zinc-200 transition hover:bg-zinc-700">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- ═══════════════════ HERO SECTION ═══════════════════ --}}
    <section class="relative overflow-hidden">
        {{-- Background --}}
        @if ($hasCover)
            <div class="hero-bg absolute inset-0" style="background-image: url('{{ asset('storage/' . $coverPath) }}')"></div>
            <div class="absolute inset-0 bg-zinc-950/85"></div>
        @else
            <div class="absolute inset-0 bg-zinc-950"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_0%,rgba(16,185,129,0.12),transparent)]"></div>
        @endif

        {{-- Content --}}
        <div class="relative mx-auto max-w-6xl px-5 pb-16 pt-16 sm:px-8 sm:pb-20 sm:pt-20 md:pb-28 md:pt-24">
            <div class="flex flex-col items-center text-center sm:flex-row sm:items-end sm:text-left sm:gap-8">
                {{-- Avatar --}}
                <div class="mb-6 shrink-0 sm:mb-0">
                    @if ($hasAvatar)
                        <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $creator->name }}"
                             class="h-28 w-28 rounded-2xl border-4 border-white/20 object-cover shadow-2xl ring-1 ring-white/10 sm:h-36 sm:w-36 sm:rounded-3xl md:h-40 md:w-40"
                             loading="eager" width="160" height="160" />
                    @else
                        <div class="flex h-28 w-28 items-center justify-center rounded-2xl border-4 border-white/20 bg-emerald-600 text-4xl font-bold text-white shadow-2xl sm:h-36 sm:w-36 sm:rounded-3xl sm:text-5xl md:h-40 md:w-40">{{ $initials }}</div>
                    @endif
                </div>

                {{-- Text --}}
                <div class="min-w-0 flex-1">
                    @if ($v('about', 'headline') && $headline)
                        <h1 class="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl lg:text-[3.25rem] leading-tight">{{ $headline }}</h1>
                    @else
                        <h1 class="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl lg:text-[3.25rem] leading-tight">{{ $creator->name }}</h1>
                    @endif
                    @if ($v('about', 'tagline') && $tagline)
                        <p class="mt-3 text-base text-white/80 sm:text-lg md:text-xl max-w-2xl">{{ $tagline }}</p>
                    @endif

                    {{-- CTA Buttons --}}
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:gap-4 sm:mt-8">
                        @if ($hasWhatsapp)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-2.5 rounded-full bg-[#25D366] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-600/30 transition hover:bg-[#20BD5A] hover:shadow-green-600/40 active:scale-[0.97]">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Chat on WhatsApp
                            </a>
                        @endif
                        @if ($hasContact && !$hasWhatsapp)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $centerContact) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-zinc-100 px-6 py-3 text-sm font-bold text-zinc-900 shadow-lg transition hover:bg-white active:scale-[0.97]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Call us
                            </a>
                        @endif
                        @if ($hasWebsite)
                            <a href="{{ $centerWebsite }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center rounded-full border-2 border-white/30 px-6 py-3 text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white/10 hover:border-white/50 active:scale-[0.97]">
                                Visit website
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom wave --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="block h-[30px] w-full sm:h-[40px] md:h-[60px]">
                <path d="M0 60h1440V30C1200 55 960 0 720 30S240 55 0 30v30z" fill="#18181b"/>
            </svg>
        </div>
    </section>

    {{-- STATS BAR - Dark --}}
    <section class="relative z-10 -mt-1 bg-zinc-950">
        <div class="mx-auto max-w-4xl px-5 sm:px-8">
            <div class="dark-card flex items-center justify-center gap-6 rounded-2xl px-6 py-5 shadow-2xl sm:gap-10 sm:px-10 sm:py-6 -mt-6 sm:-mt-8">
                <div class="text-center">
                    <div class="text-2xl font-extrabold text-emerald-400 sm:text-3xl">{{ $stats['public_quizzes'] ?? 0 }}</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-zinc-400">Quizzes</div>
                </div>
                <div class="h-8 w-px bg-zinc-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-extrabold text-emerald-400 sm:text-3xl">{{ $stats['public_questions'] ?? 0 }}</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-zinc-400">Questions</div>
                </div>
                @if (count($selectedStudents) > 0)
                    <div class="h-8 w-px bg-zinc-600"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-amber-400 sm:text-3xl">{{ count($selectedStudents) }}+</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-zinc-400">Selections</div>
                    </div>
                @endif
                @if (count($faculty) > 0)
                    <div class="hidden h-8 w-px bg-zinc-600 sm:block"></div>
                    <div class="hidden text-center sm:block">
                        <div class="text-2xl font-extrabold text-emerald-400 sm:text-3xl">{{ count($faculty) }}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-zinc-400">Faculty</div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════ ABOUT SECTION ═══════════════════ --}}
    @if ($v('about') && $v('about', 'bio') && $bio)
        <section class="fade-up bg-zinc-900 py-16 sm:py-20">
            <div class="mx-auto max-w-3xl px-5 text-center sm:px-8">
                <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-400">About us</span>
                <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl md:text-4xl">
                    @if ($v('institute','name') && $centerName) {{ $centerName }} @else Know more about us @endif
                </h2>
                <div class="mx-auto mt-3 section-divider"></div>
                <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-zinc-400 whitespace-pre-line sm:text-lg sm:leading-8">{{ $bio }}</p>
            </div>
        </section>
    @endif

    {{-- ═══════════════════ COURSES SECTION ═══════════════════ --}}
    @if (in_array('courses', $instFields))
        <section class="fade-up bg-zinc-950 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 sm:px-8">
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-zinc-800 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-400">What we offer</span>
                    <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl">Courses &amp; programmes</h2>
                    <div class="mx-auto mt-3 section-divider"></div>
                </div>
                <div class="mt-10 flex flex-wrap justify-center gap-3 sm:gap-4">
                    @foreach (preg_split('/[\n,]+/', $coursesOffered) as $course)
                        @php $course = trim($course); @endphp
                        @if ($course !== '')
                            <div class="dark-card rounded-2xl px-5 py-3.5 text-sm font-semibold text-zinc-200 transition hover:border-emerald-500/50 sm:px-6 sm:py-4 sm:text-base">
                                {{ $course }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════ FACULTY SECTION ═══════════════════ --}}
    @if ($v('faculty') && count($faculty) > 0)
        <section class="fade-up bg-zinc-900 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 sm:px-8">
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-400">Our team</span>
                    <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl">Expert faculty</h2>
                    <p class="mt-2 text-sm text-zinc-400 sm:text-base">Learn from the best educators in the field.</p>
                    <div class="mx-auto mt-3 section-divider"></div>
                </div>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 sm:gap-6">
                    @foreach ($faculty as $member)
                        <div class="hover-lift dark-card group rounded-2xl p-6 text-center sm:p-8">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-bold text-white shadow-lg transition group-hover:scale-110">
                                {{ mb_substr($member['name'] ?? '', 0, 1) ?: '?' }}
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-bold text-zinc-100">{{ $member['name'] ?? '' }}</h3>
                                @if (!empty($member['role']))
                                    <p class="mt-1 text-sm font-semibold text-emerald-400">{{ $member['role'] }}</p>
                                @endif
                                @if (!empty($member['bio']))
                                    <p class="mt-3 text-sm leading-relaxed text-zinc-400">{{ $member['bio'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════ ACHIEVERS SECTION ═══════════════════ --}}
    @if ($v('achievers') && count($selectedStudents) > 0)
        <section class="fade-up relative overflow-hidden bg-zinc-950 py-16 sm:py-20">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_0%,rgba(16,185,129,0.1),transparent)]"></div>
            <div class="relative mx-auto max-w-6xl px-5 sm:px-8">
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-amber-500/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-amber-300">Success stories</span>
                    <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl md:text-4xl">Our achievers</h2>
                    <p class="mt-2 text-sm text-zinc-400 sm:text-base">Students who made us proud.</p>
                    <div class="mx-auto mt-3 h-[3px] w-12 rounded-full bg-emerald-500"></div>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-5">
                    @foreach ($selectedStudents as $student)
                        <div class="hover-lift dark-card group flex items-center gap-4 rounded-2xl px-5 py-4 transition hover:border-emerald-500/30 sm:px-6 sm:py-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-lg font-bold text-amber-300 transition group-hover:bg-amber-500/30">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-zinc-100">{{ $student['name'] ?? '' }}</div>
                                @if (!empty($student['year']) || !empty($student['post']))
                                    <div class="mt-0.5 text-sm text-zinc-400">
                                        @if (!empty($student['post']))<span class="font-semibold text-amber-300">{{ $student['post'] }}</span>@endif
                                        @if (!empty($student['year']) && !empty($student['post'])) &middot; @endif
                                        @if (!empty($student['year'])){{ $student['year'] }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════ GALLERY SECTION ═══════════════════ --}}
    @if ($v('images') && $v('images', 'gallery') && count($galleryImages) > 0)
        <section class="fade-up bg-zinc-900 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 sm:px-8">
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-zinc-800 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-zinc-300">Life at our institute</span>
                    <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl">Gallery</h2>
                    <p class="mt-2 text-sm text-zinc-400 sm:text-base">Classroom, events &amp; more.</p>
                    <div class="mx-auto mt-3 section-divider"></div>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5">
                    @foreach ($galleryImages as $path)
                        @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                            <div class="gallery-item group overflow-hidden rounded-2xl ring-1 ring-zinc-600 transition hover:ring-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/10">
                                <img src="{{ asset('storage/' . $path) }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" />
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════ INSTITUTE / CONTACT SECTION ═══════════════════ --}}
    @if (count($instFields) > 0)
        <section class="fade-up bg-zinc-950 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 sm:px-8">
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-400">Get in touch</span>
                    <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl">Visit or contact us</h2>
                    <div class="mx-auto mt-3 section-divider"></div>
                </div>

                <div class="mx-auto mt-10 max-w-3xl overflow-hidden rounded-3xl dark-card shadow-2xl">
                    <div class="grid gap-0 md:grid-cols-2">
                        {{-- Left: Info --}}
                        <div class="p-6 sm:p-8">
                            <dl class="space-y-5">
                                @if (in_array('name', $instFields))
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Institute</dt><dd class="mt-0.5 font-semibold text-zinc-200">{{ $centerName }}</dd></div>
                                    </div>
                                @endif
                                @if (in_array('address', $instFields))
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Address</dt><dd class="mt-0.5 text-sm text-zinc-300 whitespace-pre-line">{{ $centerAddress }}</dd></div>
                                    </div>
                                @endif
                                @if (in_array('city', $instFields))
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">City</dt><dd class="mt-0.5 font-semibold text-zinc-200">{{ $centerCity }}</dd></div>
                                    </div>
                                @endif
                                @if (in_array('timings', $instFields))
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Timings</dt><dd class="mt-0.5 text-sm text-zinc-300">{{ $centerTimings }}</dd></div>
                                    </div>
                                @endif
                                @if (in_array('contact', $instFields))
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Contact</dt><dd class="mt-0.5"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $centerContact) }}" class="font-semibold text-emerald-400 hover:text-emerald-300 hover:underline">{{ $centerContact }}</a></dd></div>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        {{-- Right: CTA panel --}}
                        <div class="flex flex-col items-center justify-center gap-4 border-t border-zinc-700 bg-zinc-800/50 p-6 sm:p-8 md:border-l md:border-t-0">
                            <div class="text-center">
                                <h3 class="font-display text-xl font-bold text-zinc-100">Ready to start?</h3>
                                <p class="mt-1 text-sm text-zinc-400">Connect with us today.</p>
                            </div>
                            <div class="flex flex-col gap-3 w-full max-w-[220px]">
                                @if ($hasWhatsapp)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center justify-center gap-2 rounded-full bg-[#25D366] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#20BD5A]">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                                @if ($hasContact)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $centerContact) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-600 bg-zinc-800 px-5 py-3 text-sm font-bold text-zinc-200 transition hover:bg-zinc-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        Call now
                                    </a>
                                @endif
                                @if ($hasWebsite)
                                    <a href="{{ $centerWebsite }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-600 bg-zinc-800 px-5 py-3 text-sm font-bold text-zinc-200 transition hover:bg-zinc-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                        Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- QUIZZES SECTION - Dark --}}
    <section class="fade-up bg-zinc-900 py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="text-center">
                <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-400">Practice now</span>
                <h2 class="mt-4 font-display text-2xl font-bold text-zinc-100 sm:text-3xl">Public quizzes</h2>
                <p class="mt-2 text-sm text-zinc-400 sm:text-base">Practice with {{ $creator->name }}'s quizzes.</p>
                <div class="mx-auto mt-3 section-divider"></div>
            </div>

            @if ($publicQuizzes->count() === 0)
                <div class="mx-auto mt-10 max-w-md rounded-2xl border-2 border-dashed border-zinc-600 dark-card p-10 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-zinc-700">
                        <svg class="h-7 w-7 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                    </div>
                    <p class="mt-4 font-semibold text-zinc-300">No public quizzes yet</p>
                    <p class="mt-1 text-sm text-zinc-500">Check back soon for new practice material.</p>
                </div>
            @else
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 sm:gap-6">
                    @foreach ($publicQuizzes as $quiz)
                        <a href="{{ route('public.quizzes.show', $quiz->unique_code) }}"
                           class="hover-lift dark-card group flex flex-col rounded-2xl p-5 text-left transition hover:border-emerald-500/50 sm:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-zinc-100 break-words group-hover:text-emerald-400 leading-snug">{{ $quiz->title }}</h3>
                                    @if ($quiz->description)
                                        <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-zinc-400">{{ $quiz->description }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 rounded-lg bg-emerald-500/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-400">{{ $quiz->mode }}</span>
                            </div>
                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-zinc-600">
                                <span class="text-xs font-medium text-zinc-500">{{ $quiz->questions_count ?? 0 }} questions</span>
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-400 group-hover:text-emerald-300">
                                    Play
                                    <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $publicQuizzes->links() }}</div>
            @endif
        </div>
    </section>

    {{-- ═══════════════════ SOCIAL LINKS ═══════════════════ --}}
    @if ($v('social') && !empty($social))
        <section class="bg-zinc-950 py-12">
            <div class="mx-auto max-w-6xl px-5 text-center sm:px-8">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Follow us on</h3>
                <div class="mt-4 flex flex-wrap justify-center gap-3">
                    @foreach ($social as $label => $url)
                        @if (is_string($url) && $url !== '')
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 rounded-full dark-card px-5 py-2.5 text-sm font-semibold text-zinc-200 transition hover:border-emerald-500/50 hover:text-emerald-400 active:scale-[0.97]">
                                @php
                                    $lbl = is_string($label) && $label !== '' ? strtolower($label) : 'link';
                                @endphp
                                @if (str_contains($lbl, 'youtube'))
                                    <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                @elseif (str_contains($lbl, 'instagram'))
                                    <svg class="h-4 w-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                @elseif (str_contains($lbl, 'telegram'))
                                    <svg class="h-4 w-4 text-sky-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                @elseif (str_contains($lbl, 'twitter') || str_contains($lbl, 'x'))
                                    <svg class="h-4 w-4 text-zinc-300" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                @elseif (str_contains($lbl, 'facebook'))
                                    <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                @else
                                    <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @endif
                                {{ is_string($label) && $label !== '' ? $label : 'Link' }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FOOTER - Dark --}}
    <footer class="border-t border-zinc-800 bg-zinc-950 py-8">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-2">
                    <div class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-600 text-xs font-bold text-white">Q</div>
                    <span class="text-sm font-semibold text-zinc-300">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</span>
                </div>
                <p class="text-xs text-zinc-500">&copy; {{ date('Y') }} {{ $v('institute','name') && $centerName ? $centerName : config('app.name', 'QuizWhiz') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Scroll animation --}}
    <script>
    (function() {
        var els = document.querySelectorAll('.fade-up');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function(el) { el.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function(el) { observer.observe(el); });
    })();
    </script>
</body>
</html>
