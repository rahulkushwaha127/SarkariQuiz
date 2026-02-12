@extends('layouts.student')

@section('title', 'About')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">About</div>
        <div class="mt-2 text-sm text-stone-600">
            @include('shared.pages.about')
        </div>
    </div>
</div>
@endsection


