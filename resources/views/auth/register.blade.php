@extends('layouts.auth')

@section('title', 'Client Registration')

@section('content')
    <h2 class="auth-title">Create Client Account</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input" placeholder="John Doe">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="john@example.com">
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Phone Number (Optional)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+91 9876543210">
        </div>

        <div class="form-group">
            <label for="company_name" class="form-label">Company / Organization (Optional)</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" class="form-input" placeholder="Acme Technologies">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="Minimum 8 characters">
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input" placeholder="Repeat password">
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">Create Account</button>
    </form>
@endsection

@section('footer')
    <div class="auth-footer">
        Already registered? <a href="{{ route('login') }}">Sign In</a>
    </div>
@endsection
