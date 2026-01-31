@extends('layouts.student')

@section('title', 'Privacy Policy')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Privacy Policy</div>
        <div class="mt-2 space-y-2 text-sm text-slate-300">
            @include('shared.pages.privacy')
        </div>
    </div>
</div>
@endsection

