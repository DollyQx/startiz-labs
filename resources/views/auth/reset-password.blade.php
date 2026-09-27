@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')
    <h2 class="auth-title">Set New Password</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus class="form-input">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="Minimum 8 characters">
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input">
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">Reset Password</button>
    </form>
@endsection
