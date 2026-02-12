@extends('layouts.student')

@section('title', 'Cookie Policy')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Cookie Policy</div>
        <div class="mt-2 space-y-2 text-sm text-stone-600">
            @include('shared.pages.cookie')
        </div>
    </div>
</div>
@endsection
