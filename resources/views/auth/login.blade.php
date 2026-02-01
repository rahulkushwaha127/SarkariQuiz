@extends('layouts.student')

@section('title', 'Login')

@section('content')
    <div class="border border-white/10 bg-white/5 p-4">
        @include('auth._student_auth', ['active' => 'login'])
    </div>
@endsection
