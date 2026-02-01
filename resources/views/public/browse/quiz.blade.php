@extends('public.browse._layout')

@section('title', $quiz->title)

@section('content')
<h1 style="margin: 0 0 6px;">{{ $quiz->title }}</h1>
@if($quiz->description)
    <p style="margin:0 0 10px; color:#555;">{{ $quiz->description }}</p>
@endif

<div style="margin: 10px 0; color:#555;">
    <div>Creator: {{ $quiz->user?->name ?? '—' }}</div>
    <div>Exam: {{ $quiz->exam?->name ?? '—' }}</div>
    <div>Subject: {{ $quiz->subject?->name ?? '—' }}</div>
    <div>Topic: {{ $quiz->topic?->name ?? '—' }}</div>
    <div>Mode: {{ $quiz->mode }}</div>
    <div>Difficulty: {{ $quiz->difficulty }}</div>
    <div>Code: <code>{{ $quiz->unique_code }}</code></div>
</div>

<p style="margin-top: 14px;">
    <a href="{{ route('public.quizzes.play', $quiz) }}">Play as guest</a>
    <span style="color:#777;"> · </span>
    <a href="{{ route('login') }}">Login</a>
</p>
@endsection

