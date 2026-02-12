@extends('layouts.student')

@section('title', 'Join Club')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Join club</div>
            <div class="mt-1 text-sm text-stone-600">Approval is required by the club admin.</div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">{{ $club->name }}</div>
            <div class="mt-1 text-xs text-stone-500">Status: {{ $club->status }}</div>
        </div>

        @if($member)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="text-sm font-semibold text-stone-800">You are already a member.</div>
                <div class="mt-3">
                    <a href="{{ route('clubs.show', $club) }}" class="block rounded-xl bg-indigo-500 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        Open club
                    </a>
                </div>
            </div>
        @else
            @if($joinRequest && $joinRequest->status === 'pending')
                <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="text-sm font-semibold text-stone-800">Request pending</div>
                    <div class="mt-1 text-sm text-stone-600">Waiting for approval.</div>
                </div>
            @elseif($joinRequest && $joinRequest->status === 'rejected')
                <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="text-sm font-semibold text-stone-800">Request rejected</div>
                    <div class="mt-1 text-sm text-stone-600">You can request again.</div>
                </div>
            @endif

            <form method="POST" action="{{ route('clubs.request_join', $club) }}" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                @csrf
                <button class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                    Request to join
                </button>
            </form>
        @endif
    </div>
@endsection
