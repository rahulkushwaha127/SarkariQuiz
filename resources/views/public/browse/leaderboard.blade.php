@extends('public.browse._layout')

@section('title', 'Leaderboard')

@section('content')
<h1 style="margin: 0 0 10px;">Leaderboard</h1>
<div style="margin: 0 0 14px; color:#555;">
    {{ $label }}@if(($exam ?? null)?->name) · {{ $exam->name }}@endif
</div>

<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
    <a href="{{ route('public.leaderboard', ['period' => 'daily', 'exam_id' => $examId]) }}">Daily</a>
    <a href="{{ route('public.leaderboard', ['period' => 'weekly', 'exam_id' => $examId]) }}">Weekly</a>
    <a href="{{ route('public.leaderboard', ['period' => 'monthly', 'exam_id' => $examId]) }}">Monthly</a>
    <a href="{{ route('public.leaderboard', ['period' => 'all', 'exam_id' => $examId]) }}">All time</a>
</div>

<form method="GET" action="{{ route('public.leaderboard') }}" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
    <input type="hidden" name="period" value="{{ $period }}">
    <select name="exam_id" style="padding:8px; border:1px solid #ddd; min-width:220px;">
        <option value="">All exams</option>
        @foreach(($exams ?? collect()) as $e)
            <option value="{{ $e->id }}" @selected((int)($examId ?? 0) === (int)$e->id)>{{ $e->name }}</option>
        @endforeach
    </select>
    <button style="padding:8px 12px; border:1px solid #ddd; background:#fff; cursor:pointer;">Apply</button>
</form>

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

