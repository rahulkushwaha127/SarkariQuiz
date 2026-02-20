@extends('layouts.admin')

@section('title', 'Content · ' . $filename)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.content.index', ['subject' => $subject, 'language' => $language, 'topic' => $topic]) }}"
               class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to list</a>
            <h1 class="mt-1 text-xl font-semibold tracking-tight text-slate-900">{{ $filename }}</h1>
            <p class="mt-0.5 font-mono text-xs text-slate-500">{{ $relativePath }}</p>
        </div>
    </div>

    @if($error)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ $error }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($questions as $q)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">#{{ $q['index'] }}</span>
                </div>
                <div class="text-sm font-medium text-slate-800">
                    {!! nl2br(e($q['prompt'])) !!}
                </div>
                <ul class="mt-2 space-y-1 pl-4">
                    @foreach($q['answers'] as $i => $ans)
                        <li class="text-sm text-slate-700 {{ $q['correct'] !== null && (int)$i === (int)$q['correct'] ? 'font-semibold text-emerald-700' : '' }}">
                            {{ $i + 1 }}. {{ $ans }}
                            @if($q['correct'] !== null && (int)$i === (int)$q['correct'])
                                <span class="ml-1 text-xs text-emerald-600">(correct)</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                @if(!empty($q['explanation']))
                    <div class="mt-2 border-t border-slate-100 pt-2 text-xs text-slate-600">
                        <span class="font-medium text-slate-500">Explanation:</span>
                        {!! nl2br(e($q['explanation'])) !!}
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                No questions in this file.
            </div>
        @endforelse
    </div>
</div>
@endsection
