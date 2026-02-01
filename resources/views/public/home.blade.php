@extends('public.browse._layout')

@section('title', 'Home')

@section('content')
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px;">
        <a href="{{ route('public.exams.index') }}">Browse exams</a>
        <span style="color:#777;">·</span>
        <a href="{{ route('public.daily') }}">Daily challenge</a>
        <span style="color:#777;">·</span>
        <a href="{{ route('public.contests.index') }}">Contests</a>
        <span style="color:#777;">·</span>
        <a href="{{ route('public.leaderboard') }}">Leaderboard</a>
    </div>

    <div style="border:1px solid #ddd; padding:12px;">
        <div style="font-weight:700; margin-bottom:6px;">
            Start your government exam prep today
        </div>
        <div style="color:#555; font-size:13px;">
            Practice topic-wise quizzes, play daily challenge, and compete in contests. Works without login (guest play).
        </div>
    </div>

    <div style="margin-top:14px; border:1px solid #ddd; padding:12px;">
        <div style="font-weight:700; margin-bottom:6px;">Today’s Daily Challenge</div>
        @if($daily && $daily->quiz)
            <div style="color:#555; font-size:13px; margin-bottom:10px;">
                <strong>{{ $daily->quiz->title }}</strong>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('public.daily') }}">View</a>
                <span style="color:#777;">·</span>
                <a href="{{ route('public.quizzes.play', $daily->quiz) }}">Play as guest</a>
                <span style="color:#777;">·</span>
                <a href="{{ route('login') }}">Login</a>
            </div>
        @else
            <div style="color:#555; font-size:13px;">No daily challenge selected yet.</div>
        @endif
    </div>

    <div style="margin-top:14px; border:1px solid #ddd; padding:12px;">
        <div style="font-weight:700; margin-bottom:10px;">Popular exams</div>
        @if(($exams ?? collect())->isEmpty())
            <div style="color:#555; font-size:13px;">No exams yet.</div>
        @else
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:10px;">
                @foreach($exams as $exam)
                    <div style="border:1px solid #eee; padding:10px;">
                        <div style="font-weight:700;">{{ $exam->name }}</div>
                        <div style="color:#777; font-size:12px; margin-top:4px;">
                            Subjects: {{ (int)($exam->subjects_count ?? 0) }}
                        </div>
                        <div style="margin-top:8px;">
                            <a href="{{ route('public.exams.show', $exam) }}">Open</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="margin-top:14px; border:1px solid #ddd; padding:12px;">
        <div style="font-weight:700; margin-bottom:10px;">Latest quizzes</div>
        @if(($latestQuizzes ?? collect())->isEmpty())
            <div style="color:#555; font-size:13px;">No public quizzes yet.</div>
        @else
            <div style="display:grid; grid-template-columns: 1fr; gap:10px;">
                @foreach($latestQuizzes as $q)
                    <div style="border:1px solid #eee; padding:10px;">
                        <div style="font-weight:700;">{{ $q->title }}</div>
                        <div style="color:#777; font-size:12px; margin-top:4px;">
                            {{ $q->exam?->name ?? '—' }} · {{ $q->subject?->name ?? '—' }}
                            · Questions: {{ (int)($q->questions_count ?? 0) }}
                            · Plays: {{ (int)($q->plays_count ?? 0) }}
                        </div>
                        <div style="margin-top:8px; display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('public.quizzes.show', $q) }}">Details</a>
                            <span style="color:#777;">·</span>
                            <a href="{{ route('public.quizzes.play', $q) }}">Play as guest</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="margin-top:14px; border:1px solid #ddd; padding:12px;">
        <div style="font-weight:700; margin-bottom:10px;">Live & upcoming contests</div>
        @if(($contests ?? collect())->isEmpty())
            <div style="color:#555; font-size:13px;">No public contests right now.</div>
        @else
            <div style="display:grid; grid-template-columns: 1fr; gap:10px;">
                @foreach($contests as $c)
                    <div style="border:1px solid #eee; padding:10px;">
                        <div style="font-weight:700;">{{ $c->title }}</div>
                        <div style="color:#777; font-size:12px; margin-top:4px;">
                            Status: {{ $c->status }} · Participants: {{ (int)($c->participants_count ?? 0) }}
                            @if($c->starts_at) · Starts: {{ $c->starts_at->format('d M, H:i') }} @endif
                        </div>
                        <div style="margin-top:8px;">
                            <a href="{{ route('public.contests.show', $c) }}">Open</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

