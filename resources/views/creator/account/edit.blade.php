@extends('layouts.creator')

@section('title', 'Profile')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Profile</h1>
        <p class="mt-1 text-sm text-slate-600">Your account details. Change password only if you want to update it.</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('creator.profile.update') }}" class="max-w-xl space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('email') border-red-300 @enderror">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">New password</label>
            <input type="password" name="password" autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('password') border-red-300 @enderror"
                   placeholder="Leave blank to keep current">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Confirm new password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
            <a href="{{ route('creator.dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
