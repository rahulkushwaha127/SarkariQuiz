@extends('layouts.student')

@section('title', 'Join Contest')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Join a room</div>
        <div class="mt-1 text-sm text-stone-600">Enter the contest code to join.</div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <form method="POST" action="{{ route('contests.join.submit') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700">Room code</label>
                <input name="code"
                       value="{{ old('code') }}"
                       placeholder="AB12CD"
                       class="mt-1 w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-base text-stone-800 placeholder:text-stone-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('code') border-red-400 @enderror"
                       required>
                @error('code') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <button class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Join now
            </button>
        </form>

        <div class="mt-3 text-xs text-stone-500">
            Invite link also works: opening it will auto-join you.
        </div>
    </div>
</div>
@endsection
