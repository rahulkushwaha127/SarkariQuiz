@extends('public.browse._layout')

@section('title', 'Exams')

@section('content')
<h1 style="margin: 0 0 10px;">Exams</h1>
<p style="margin: 0 0 14px; color:#555;">Browse exams and subjects.</p>

@if(($exams ?? collect())->isEmpty())
    <p>No exams yet.</p>
@else
    <ul style="margin:0; padding-left: 18px;">
        @foreach($exams as $exam)
            <li style="margin: 6px 0;">
                <a href="{{ route('public.exams.show', $exam) }}">{{ $exam->name }}</a>
            </li>
        @endforeach
    </ul>
@endif
@endsection

