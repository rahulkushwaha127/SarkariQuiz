@extends('public.browse._layout')

@section('title', 'Leaderboard')

@section('content')
<h1 style="margin: 0 0 10px;">Leaderboard</h1>
<div style="margin: 0 0 14px; color:#555;">
    {{ $label }}
</div>

<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
    <a href="{{ route('public.leaderboard', ['period' => 'daily']) }}">Daily</a>
    <a href="{{ route('public.leaderboard', ['period' => 'weekly']) }}">Weekly</a>
    <a href="{{ route('public.leaderboard', ['period' => 'all']) }}">All time</a>
</div>

@if(($rows ?? collect())->isEmpty())
    <p>No attempts yet.</p>
@else
    <table style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Rank</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">User</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Score</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Attempts</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee;">#{{ $loop->iteration }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $row->user_name }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ (int) $row->total_score }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ (int) $row->attempts }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@endsection

