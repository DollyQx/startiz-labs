@extends('layouts.auth')

@section('title', 'Admin Portal Login')

@section('content')
    <div style="text-align: center; margin-bottom: 1.25rem;">
        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
            Administrative Access Only
        </span>
    </div>

    <h2 class="auth-title">Admin Portal Sign In</h2>

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Administrator Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="admin@gmcodelab.com">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="••••••••">
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
        </div>

        <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #ef4444, #dc2626); margin-top: 0.5rem;">
            Authenticate Admin Session
        </button>
    </form>
@endsection

@section('footer')
    <div class="auth-footer">
        Are you a client? <a href="{{ route('login') }}">Client Portal Login</a>
    </div>
@endsection
