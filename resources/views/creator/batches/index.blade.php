@extends('layouts.creator')

@section('title', 'Batches')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Batches</h1>
            <p class="mt-1 text-sm text-slate-600">Create batches, add students, assign quizzes, and track performance.</p>
        </div>
        <a href="{{ route('creator.batches.create') }}"
           class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            + Create batch
        </a>
    </div>

    @if(session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    @if($batches->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
            <h3 class="text-base font-semibold text-slate-700">No batches yet</h3>
            <p class="mt-1 text-sm text-slate-500">Create your first batch to start managing students and assigning quizzes.</p>
            <a href="{{ route('creator.batches.create') }}"
               class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                Create batch
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($batches as $batch)
                <a href="{{ route('creator.batches.show', $batch) }}"
                   class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="truncate text-base font-semibold text-slate-900">{{ $batch->name }}</h3>
                                @if($batch->status === 'archived')
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Archived</span>
                                @endif
                            </div>
                            @if($batch->description)
                                <p class="mt-1 truncate text-sm text-slate-500">{{ $batch->description }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 items-center gap-4 text-sm text-slate-500">
                            <span>{{ $batch->active_students_count }} students</span>
                            <span>{{ $batch->quizzes_count }} quizzes</span>
                            <span class="font-mono text-xs text-slate-400">{{ $batch->join_code }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div>{{ $batches->links() }}</div>
    @endif
</div>
@endsection
