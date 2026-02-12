@extends('layouts.student')

@section('title', 'Sign up')

@section('content')
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        @include('auth._student_auth', ['active' => 'register'])
    </div>
@endsection
