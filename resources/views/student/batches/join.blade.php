@extends('layouts.student')

@section('title', 'Join Batch')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Join a batch</div>
        <div class="mt-1 text-sm text-stone-600">Enter the batch code your teacher shared with you.</div>
    </div>

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <form method="POST" action="{{ route('batches.join.submit') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700">Batch code</label>
                <input name="code"
                       value="{{ old('code') }}"
                       placeholder="ABC123"
                       class="mt-1 w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-base text-stone-800 placeholder:text-stone-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('code') border-red-400 @enderror"
                       required>
                @error('code') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <button class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Join batch
            </button>
        </form>

        <div class="mt-3 text-xs text-stone-500">
            You can also join by opening the invite link your teacher shared.
        </div>
    </div>
</div>
@endsection
