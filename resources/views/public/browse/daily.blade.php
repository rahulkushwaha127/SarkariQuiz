@extends('public.browse._layout')

@section('title', 'Daily Challenge')

@section('content')
    <h1 style="margin: 0 0 10px;">Daily Challenge</h1>
    <div style="margin: 0 0 14px; color:#555;">
        Date: {{ $today }}
    </div>

    @if(!$daily?->quiz)
        <p>No daily challenge yet.</p>
    @else
        <div style="border:1px solid #ddd; padding: 12px; margin-bottom: 14px;">
            <div style="font-weight:700; margin-bottom: 6px;">{{ $daily->quiz->title }}</div>
            <div style="color:#555; margin-bottom: 10px;">By: {{ $daily->quiz->user?->name ?? '—' }}</div>
            <a href="{{ route('public.quizzes.play', $daily->quiz) }}">Play as guest</a>
            <span style="color:#777;"> · </span>
            <a href="{{ route('login') }}">Login</a>
        </div>

        <h2 style="margin: 0 0 10px; font-size: 16px;">Leaderboard (today)</h2>
        @if(($rows ?? collect())->isEmpty())
            <p>No attempts yet.</p>
        @else
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Rank</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">User</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Best score</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Best time</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #eee;">#{{ $loop->iteration }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ $row->user_name }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ (int) $row->best_score }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ (int) $row->best_time }}s</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endif
@endsection

