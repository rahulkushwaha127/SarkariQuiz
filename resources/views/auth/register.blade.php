@extends('layouts.student')

@section('title', 'Sign up')

@section('content')
    <div class="border border-white/10 bg-white/5 p-4">
        @include('auth._student_auth', ['active' => 'register'])
    </div>
@endsection
