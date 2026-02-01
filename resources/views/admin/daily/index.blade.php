@extends('layouts.admin')

@section('title', 'Daily Challenge')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Daily Challenge</h1>
                <p class="mt-1 text-sm text-slate-600">Pick a published public quiz for today (or any date).</p>
            </div>
            <a href="{{ route('public.daily') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                View public page
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-slate-900">Current ({{ $today }})</div>
            <div class="mt-2 text-sm text-slate-700">
                @if($current?->quiz)
                    <div><span class="text-slate-500">Quiz:</span> <span class="font-semibold text-slate-900">{{ $current->quiz->title }}</span></div>
                    <div><span class="text-slate-500">Code:</span> <code class="text-slate-900">{{ $current->quiz->unique_code }}</code></div>
                    <div><span class="text-slate-500">Active:</span> {{ $current->is_active ? 'Yes' : 'No' }}</div>
                @else
                    <div class="text-slate-500">No daily challenge set for today.</div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-slate-900">Set daily challenge</div>

            <form class="mt-4 space-y-4" method="POST" action="{{ route('admin.daily.store') }}">
                @csrf

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Date</label>
                        <input type="date" name="challenge_date" value="{{ old('challenge_date', $today) }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('challenge_date') border-red-300 @enderror">
                        @error('challenge_date') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Quiz (published + public)</label>
                        <select name="quiz_id"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('quiz_id') border-red-300 @enderror">
                            <option value="">Select a quiz…</option>
                            @foreach($quizzes as $q)
                                <option value="{{ $q->id }}" @selected(old('quiz_id') == $q->id)>
                                    {{ $q->title }} ({{ $q->unique_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('quiz_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300" checked>
                    Active
                </label>

                <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save
                </button>
            </form>
        </div>
    </div>
@endsection

