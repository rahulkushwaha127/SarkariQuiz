@extends('layouts.creator')

@section('title', 'Edit Batch')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit batch</h1>
        <p class="mt-1 text-sm text-slate-600">Update batch details or archive it.</p>
    </div>

    <form method="POST" action="{{ route('creator.batches.update', $batch) }}" class="max-w-xl space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Batch name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $batch->name) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" required />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('description', $batch->description) }}</textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select name="status" id="status"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="active" @selected(old('status', $batch->status) === 'active')>Active</option>
                    <option value="archived" @selected(old('status', $batch->status) === 'archived')>Archived</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Join code</label>
                <div class="mt-1 rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-sm font-mono text-slate-700">{{ $batch->join_code }}</div>
                <p class="mt-1 text-xs text-slate-500">Share this code with students so they can join.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save</button>
            <a href="{{ route('creator.batches.show', $batch) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
