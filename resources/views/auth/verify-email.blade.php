@extends('layouts.auth')

@section('title', 'Verify Your Email')

@section('content')
    <h2 class="auth-title">Verify Email Address</h2>

    <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem; text-align: center; line-height: 1.5;">
        Thanks for joining Startiz Labs! Before getting started, please verify your email address by clicking on the link we just emailed to you.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary">Resend Verification Email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem; text-align: center;">
        @csrf
        <button type="submit" style="background: none; border: none; color: var(--text-muted); font-size: 0.875rem; cursor: pointer;">
            Log Out
        </button>
    </form>
@endsection
