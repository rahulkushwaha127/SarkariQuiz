@extends('layouts.student')

@section('title', 'Join Contest')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Join a room</div>
        <div class="mt-1 text-sm text-slate-300">Enter the contest code to join.</div>
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <form method="POST" action="{{ route('contests.join.submit') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-200">Room code</label>
                <input name="code"
                       value="{{ old('code') }}"
                       placeholder="AB12CD"
                       class="mt-1 w-full border border-white/10 bg-slate-950/40 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none @error('code') border-red-400/60 @enderror"
                       required>
                @error('code') <div class="mt-1 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <button class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                Join now
            </button>
        </form>

        <div class="mt-3 text-xs text-slate-300">
            Invite link also works: opening it will auto-join you.
        </div>
    </div>
</div>
@endsection


