@extends('layouts.creator')

@section('title', 'Notify Students')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Notify students</h1>
            <p class="mt-1 text-sm text-slate-600">Send in-app notifications (and optional push) to your audience.</p>
        </div>

        @error('notify')
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
        @enderror

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="POST" action="{{ route('creator.notifications.send') }}" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Title</label>
                        <input name="title" value="{{ old('title') }}" required
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Click URL (optional)</label>
                        <input name="url" value="{{ old('url') }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                               placeholder="e.g. /student/daily">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Message</label>
                    <textarea name="body" rows="4" required
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">{{ old('body') }}</textarea>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold text-slate-900">Audience</div>
                    <div class="mt-3 grid gap-2 md:grid-cols-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="audience[]" value="quiz_players" class="h-4 w-4 rounded border-slate-300"
                                   @checked(in_array('quiz_players', (array) old('audience', ['quiz_players']), true))>
                            Quiz players
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="audience[]" value="contest_participants" class="h-4 w-4 rounded border-slate-300"
                                   @checked(in_array('contest_participants', (array) old('audience', []), true))>
                            Contest participants
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="audience[]" value="club_members" class="h-4 w-4 rounded border-slate-300"
                                   @checked(in_array('club_members', (array) old('audience', []), true))>
                            Club members (clubs you own)
                        </label>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="send_push" value="1" class="h-4 w-4 rounded border-slate-300"
                           @checked(old('send_push') == '1')>
                    Also send push notification (only to users who enabled push)
                </label>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('creator.notifications.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Back
                    </a>
                    <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

