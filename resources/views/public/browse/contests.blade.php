@extends('public.browse._layout')

@section('title', 'Contests')

@section('content')
<h1 style="margin: 0 0 10px;">Public contests</h1>

@if(($contests ?? collect())->isEmpty())
    <p>No public contests yet.</p>
@else
    <table style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Contest</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Status</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Host</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Participants</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contests as $contest)
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee;">
                    <a href="{{ route('public.contests.show', $contest) }}">{{ $contest->title }}</a>
                    @if($contest->quiz)
                        <div style="color:#666; font-size: 12px;">Quiz: {{ $contest->quiz->title }}</div>
                    @endif
                </td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $contest->status }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $contest->creator?->name ?? '—' }}</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $contest->participants_count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top: 12px;">
        {{ $contests->links() }}
    </div>
@endif
@endsection

