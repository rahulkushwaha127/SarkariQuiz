@extends('layouts.creator')

@section('title', 'Create Batch')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Create batch</h1>
        <p class="mt-1 text-sm text-slate-600">A join code will be auto-generated. Share it with your students.</p>
    </div>

    <form method="POST" action="{{ route('creator.batches.store') }}" class="max-w-xl space-y-5">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Batch name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       placeholder="e.g. UPSC 2026 — Morning Batch"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" required />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700">Description (optional)</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Any notes about this batch..."
                          class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Create batch
            </button>
            <a href="{{ route('creator.batches.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
