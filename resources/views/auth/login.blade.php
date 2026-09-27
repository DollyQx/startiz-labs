@extends('layouts.auth')

@section('title', 'Client Portal Login')

@section('content')
    <h2 class="auth-title">Client Account Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="client@example.com">
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                <a href="{{ route('password.request') }}" style="font-size: 0.8125rem; color: #60a5fa; text-decoration: none;">Forgot Password?</a>
            </div>
            <input id="password" type="password" name="password" required class="form-input" placeholder="••••••••">
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">Sign In to Client Portal</button>
    </form>
@endsection

@section('footer')
    <div class="auth-footer">
        Don't have a client account? <a href="{{ route('register') }}">Register Here</a>
    </div>
@endsection
