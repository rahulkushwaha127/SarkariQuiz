@extends('public.browse._layout')

@section('title', $contest->title)

@section('content')
<h1 style="margin: 0 0 6px;">{{ $contest->title }}</h1>
<div style="color:#555; margin-bottom: 10px;">
    Status: {{ $contest->status }} · Host: {{ $contest->creator?->name ?? '—' }}
    @if($contest->quiz)
        · Quiz: {{ $contest->quiz->title }}
    @endif
</div>
@if($contest->starts_at)
    <div style="color:#555; margin-bottom: 6px;">Starts: {{ $contest->starts_at->format('d M Y, H:i') }}</div>
@endif
@if($contest->ends_at)
    <div style="color:#555; margin-bottom: 10px;">Ends: {{ $contest->ends_at->format('d M Y, H:i') }}</div>
@endif

<h2 style="margin: 18px 0 8px; font-size: 16px;">Leaderboard</h2>

@if(($leaderboard ?? collect())->isEmpty())
    <p>No participants yet.</p>
@else
    <table style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Rank</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">User</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Score</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Time</th>
        </tr>
        </thead>
        <tbody>
        @foreach($leaderboard as $row)
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee;">#{{ $loop->iteration }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $row->user?->name ?? '—' }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $row->score }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $row->time_taken_seconds }}s</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<p style="margin-top: 14px;">
    <a href="{{ route('login') }}">Login to join/play</a>
</p>
@endsection

