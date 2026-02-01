@extends('layouts.student')

@section('title', 'Join Club')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Join club</div>
            <div class="mt-1 text-sm text-slate-300">Approval is required by the club admin.</div>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">{{ $club->name }}</div>
            <div class="mt-1 text-xs text-slate-400">Status: {{ $club->status }}</div>
        </div>

        @if($member)
            <div class="border border-white/10 bg-white/5 p-4">
                <div class="text-sm font-semibold text-white">You are already a member.</div>
                <div class="mt-3">
                    <a href="{{ route('clubs.show', $club) }}" class="bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 block text-center">
                        Open club
                    </a>
                </div>
            </div>
        @else
            @if($joinRequest && $joinRequest->status === 'pending')
                <div class="border border-white/10 bg-white/5 p-4">
                    <div class="text-sm font-semibold text-white">Request pending</div>
                    <div class="mt-1 text-sm text-slate-300">Waiting for approval.</div>
                </div>
            @elseif($joinRequest && $joinRequest->status === 'rejected')
                <div class="border border-white/10 bg-white/5 p-4">
                    <div class="text-sm font-semibold text-white">Request rejected</div>
                    <div class="mt-1 text-sm text-slate-300">You can request again.</div>
                </div>
            @endif

            <form method="POST" action="{{ route('clubs.request_join', $club) }}" class="border border-white/10 bg-white/5 p-4">
                @csrf
                <button class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                    Request to join
                </button>
            </form>
        @endif
    </div>
@endsection


