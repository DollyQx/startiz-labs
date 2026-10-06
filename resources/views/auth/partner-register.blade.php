@extends('layouts.auth')

@section('title', 'Join the Partner Program')

@section('content')
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
            Affiliate & Channel Partners
        </span>
        <h2 class="auth-title" style="margin-bottom: 0.25rem;">Apply for Partner Portal</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Earn attractive recurring commissions referring clients to Startiz Labs</p>
    </div>

    <form method="POST" action="{{ route('partner.register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Full Name *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input" placeholder="Alex Morgan">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="alex@agency.com">
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Phone / WhatsApp Number</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+91 9876543210">
        </div>

        <div class="form-group">
            <label for="company_name" class="form-label">Company / Agency Name (Optional)</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" class="form-input" placeholder="Nova Growth Partners">
        </div>

        <div class="form-group">
            <label for="website" class="form-label">Website / LinkedIn (Optional)</label>
            <input id="website" type="text" name="website" value="{{ old('website') }}" class="form-input" placeholder="https://agency.com">
        </div>

        <div class="form-group">
            <label for="payout_method" class="form-label">Preferred Payout Method</label>
            <select id="payout_method" name="payout_method" class="form-input" style="background-color: #111827; color: #fff;">
                <option value="upi" {{ old('payout_method') === 'upi' ? 'selected' : '' }}>UPI (Instant Transfer)</option>
                <option value="bank_transfer" {{ old('payout_method') === 'bank_transfer' ? 'selected' : '' }}>Direct Bank Wire / NEFT</option>
            </select>
        </div>

        <div class="form-group">
            <label for="payout_details" class="form-label">Payout Details (UPI ID or Bank Info)</label>
            <input id="payout_details" type="text" name="payout_details" value="{{ old('payout_details') }}" class="form-input" placeholder="alex@okhdfcbank or Account + IFSC">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="Minimum 8 characters">
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password *</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input" placeholder="Repeat password">
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">Submit Application</button>
    </form>
@endsection

@section('footer')
    <div class="auth-footer">
        Already a partner? <a href="{{ route('login') }}">Sign In to Workspace</a>
    </div>
@endsection
