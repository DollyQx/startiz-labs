@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h2 class="auth-title">Reset Your Password</h2>

    <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem; text-align: center;">
        Enter your registered email address and we will send you a link to reset your password.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="client@example.com">
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">Send Password Reset Link</button>
    </form>
@endsection

@section('footer')
    <div class="auth-footer">
        Back to <a href="{{ route('login') }}">Sign In</a>
    </div>
@endsection
