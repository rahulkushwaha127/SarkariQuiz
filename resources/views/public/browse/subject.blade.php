@extends('public.browse._layout')

@section('title', $subject->name)

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
        <h1 style="margin:0;">{{ $subject->name }}</h1>
        <div style="color:#555; margin-top: 4px;">Exam: {{ $subject->exam?->name }}</div>
    </div>
    <div>
        @if($subject->exam)
            <a href="{{ route('public.exams.show', $subject->exam) }}">← Back to exam</a>
        @endif
    </div>
</div>

<div style="margin-top: 14px;">
    @if(($quizzes ?? collect())->isEmpty())
        <p>No public quizzes yet.</p>
    @else
        <table style="width:100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Quiz</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Questions</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Mode</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Play</th>
            </tr>
            </thead>
            <tbody>
            @foreach($quizzes as $quiz)
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #eee;">
                        <a href="{{ route('public.quizzes.show', $quiz) }}">{{ $quiz->title }}</a>
                    </td>
                    <td style="padding:8px; border-bottom:1px solid #eee;">{{ $quiz->questions_count }}</td>
                    <td style="padding:8px; border-bottom:1px solid #eee;">{{ $quiz->mode }}</td>
                    <td style="padding:8px; border-bottom:1px solid #eee;">
                        <a href="{{ route('public.quizzes.play', $quiz) }}">Play</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top: 12px;">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection

