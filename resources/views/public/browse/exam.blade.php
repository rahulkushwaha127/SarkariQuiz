@extends('public.browse._layout')

@section('title', $exam->name)

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
        <h1 style="margin:0;">{{ $exam->name }}</h1>
        <div style="color:#555; margin-top: 4px;">Subjects</div>
    </div>
    <div>
        <a href="{{ route('public.exams.index') }}">← All exams</a>
    </div>
</div>

<div style="margin-top: 14px;">
    @if(($subjects ?? collect())->isEmpty())
        <p>No subjects yet.</p>
    @else
        <ul style="margin:0; padding-left: 18px;">
            @foreach($subjects as $subject)
                <li style="margin: 8px 0;">
                    <a href="{{ route('public.subjects.show', $subject) }}">{{ $subject->name }}</a>
                    <span style="color:#666;">(Public quizzes: {{ $subject->public_quizzes_count ?? 0 }})</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection

