@extends('layouts.student')

@section('title', 'Create Club')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Create club</div>
            <div class="mt-1 text-sm text-stone-600">You will become the club admin.</div>
        </div>

        <form method="POST" action="{{ route('clubs.store') }}" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-stone-800">Club name</label>
                <input name="name" value="{{ old('name') }}"
                       class="mt-1 w-full rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 placeholder:text-stone-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                       placeholder="My SSC Practice Club">
                @error('name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <button class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Create
            </button>
        </form>
    </div>
@endsection
