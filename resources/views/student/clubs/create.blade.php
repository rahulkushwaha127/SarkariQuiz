@extends('layouts.student')

@section('title', 'Create Club')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Create club</div>
            <div class="mt-1 text-sm text-slate-300">You will become the club admin.</div>
        </div>

        <form method="POST" action="{{ route('clubs.store') }}" class="border border-white/10 bg-white/5 p-4 space-y-3">
            @csrf
            <div>
                <label class="text-sm font-semibold text-white">Club name</label>
                <input name="name" value="{{ old('name') }}"
                       class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white"
                       placeholder="My SSC Practice Club">
                @error('name') <div class="mt-1 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <button class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                Create
            </button>
        </form>
    </div>
@endsection


