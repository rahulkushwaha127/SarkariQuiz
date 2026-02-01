<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Result · {{ $siteName ?? config('app.name', 'QuizWhiz') }}</title>

    @php
        $title = $type === 'practice'
            ? ('Practice Result')
            : ('Quiz Result');

        $desc = $type === 'practice'
            ? ("Score: {$attempt->score}/{$attempt->total_questions}")
            : ("Score: {$attempt->score}/{$attempt->total_questions} · " . ($attempt->quiz?->title ?? 'Quiz'));

        $shareUrl = url('/s/' . $code);
        $imgUrl = url('/s/' . $code . '.png');
    @endphp

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:image" content="{{ $imgUrl }}">
    <meta property="og:url" content="{{ $shareUrl }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $desc }}">
    <meta name="twitter:image" content="{{ $imgUrl }}">
</head>
<body>
<div style="max-width: 680px; margin: 0 auto; padding: 18px 14px; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; border-bottom:1px solid #ddd; padding-bottom:10px;">
        <div><strong>{{ $siteName ?? config('app.name', 'QuizWhiz') }}</strong></div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('public.daily') }}">Daily</a>
            <a href="{{ route('public.leaderboard') }}">Leaderboard</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    </div>

    <h1 style="margin: 14px 0 10px;">Result</h1>

    <div style="border:1px solid #ddd; padding: 12px; margin-bottom: 12px;">
        @if($type === 'quiz')
            <div style="font-weight:700; margin-bottom:6px;">{{ $attempt->quiz?->title ?? 'Quiz' }}</div>
        @else
            <div style="font-weight:700; margin-bottom:6px;">Practice</div>
        @endif

        <div style="color:#444;">
            <div><strong>Score:</strong> {{ (int) $attempt->score }} / {{ (int) $attempt->total_questions }}</div>
            <div><strong>Correct:</strong> {{ (int) $attempt->correct_count }} · <strong>Wrong:</strong> {{ (int) $attempt->wrong_count }} · <strong>Unanswered:</strong> {{ (int) $attempt->unanswered_count }}</div>
            <div><strong>Time:</strong> {{ (int) $attempt->time_taken_seconds }}s</div>
        </div>
    </div>

    <div style="border:1px solid #ddd; padding: 12px;">
        <div style="margin-bottom: 8px;"><strong>Share</strong></div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="https://wa.me/?text={{ urlencode($shareUrl) }}">WhatsApp</a>
            <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}">Telegram</a>
            <a href="{{ $imgUrl }}">Open image</a>
        </div>
        <div style="margin-top:10px; color:#555; font-size: 13px;">
            Link: <code>{{ $shareUrl }}</code>
        </div>
    </div>
</div>
</body>
</html>

