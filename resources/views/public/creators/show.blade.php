<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $creator->name }} ({{ '@' . $creator->username }}) · Creator</title>

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    @php
        $bio = $creator->bio ?? $creator->creatorProfile?->bio;
        $centerName = $creator->coaching_center_name ?? $creator->creatorProfile?->coaching_center_name;
        $centerCity = $creator->coaching_city ?? $creator->creatorProfile?->coaching_city;
        $centerContact = $creator->coaching_contact ?? $creator->creatorProfile?->coaching_contact;
        $centerWebsite = $creator->coaching_website ?? $creator->creatorProfile?->coaching_website;
        $social = $creator->social_links ?? $creator->creatorProfile?->social_links ?? [];
        if (!is_array($social)) { $social = []; }
        $initials = collect(explode(' ', trim($creator->name)))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
        if ($initials === '') { $initials = 'C'; }
    @endphp

    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-x-0 top-0 h-56 bg-gradient-to-b from-indigo-600/15 to-transparent"></div>
    </div>

    <header class="border-b border-slate-200 bg-white/70 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-900 text-white font-semibold">Q</div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Creator profile</div>
                </div>
            </a>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <section class="lg:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-indigo-600 text-white text-lg font-semibold">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-semibold tracking-tight text-slate-900">{{ $creator->name }}</h1>
                            <div class="mt-1 text-sm text-slate-600">{{ '@' . $creator->username }}</div>
                        </div>
                    </div>

                    @if ($bio)
                        <p class="mt-4 text-sm leading-relaxed text-slate-700">{{ $bio }}</p>
                    @endif

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Public quizzes</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['public_quizzes'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Questions (shown)</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['public_questions'] ?? 0 }}</div>
                        </div>
                    </div>

                    @if (!empty($social))
                        <div class="mt-5">
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Social</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($social as $label => $url)
                                    @if (is_string($url) && $url !== '')
                                        <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                           href="{{ $url }}" target="_blank" rel="noopener noreferrer">
                                            {{ is_string($label) && $label !== '' ? $label : 'Link' }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Coaching center</h2>
                            <p class="mt-1 text-sm text-slate-600">Details shared by the creator.</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Name</dt>
                            <dd class="font-medium text-slate-900">{{ $centerName ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">City</dt>
                            <dd class="font-medium text-slate-900">{{ $centerCity ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Contact</dt>
                            <dd class="font-medium text-slate-900">{{ $centerContact ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Website</dt>
                            <dd class="font-medium text-slate-900">
                                @if ($centerWebsite)
                                    <a class="text-indigo-700 hover:underline" href="{{ $centerWebsite }}" target="_blank" rel="noopener noreferrer">
                                        {{ $centerWebsite }}
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Public quizzes</h2>
                            <p class="mt-1 text-sm text-slate-600">Browse quizzes published by this creator.</p>
                        </div>
                    </div>

                    @if ($publicQuizzes->count() === 0)
                        <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                            <div class="text-sm font-semibold text-slate-900">No public quizzes yet</div>
                            <div class="mt-1 text-sm text-slate-600">Check back soon.</div>
                        </div>
                    @else
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            @foreach ($publicQuizzes as $quiz)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 hover:shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-slate-900">{{ $quiz->title }}</div>
                                            @if ($quiz->description)
                                                <div class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $quiz->description }}</div>
                                            @endif
                                        </div>
                                        <div class="shrink-0 rounded-xl bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                                            {{ strtoupper($quiz->mode) }}
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                                        <span class="rounded-full bg-slate-100 px-2 py-1">Code: {{ $quiz->unique_code }}</span>
                                        <span class="rounded-full bg-slate-100 px-2 py-1">Questions: {{ $quiz->questions_count ?? '—' }}</span>
                                        <span class="rounded-full bg-slate-100 px-2 py-1">Status: {{ $quiz->status }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $publicQuizzes->links() }}
                        </div>
                    @endif
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600 shadow-sm">
                    <div class="font-semibold text-slate-900">Stats (MVP)</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><strong>Total public quizzes:</strong> {{ $stats['public_quizzes'] ?? 0 }}</li>
                        <li><strong>Total plays:</strong> Coming soon</li>
                        <li><strong>Contest participation:</strong> Coming soon</li>
                    </ul>
                </div>
            </section>
        </div>
    </main>
</body>
</html>

