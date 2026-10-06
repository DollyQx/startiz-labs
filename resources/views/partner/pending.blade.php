@extends('layouts.auth')

@section('title', 'Application Under Review — Partner Program')

@section('content')
<div style="text-align: center; margin-bottom: 2rem;">
    <div style="width: 64px; height: 64px; margin: 0 auto 1.25rem; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.3); border-radius: 50%; display: flex; items-center; justify-content: center;">
        <svg style="width: 32px; height: 32px; color: #f59e0b; margin: auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
        Pending Verification
    </span>

    <h2 class="auth-title" style="margin-bottom: 0.5rem;">Partner Application Under Review</h2>
    <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 380px; margin: 0 auto; line-height: 1.5;">
        Thank you for applying to the Startiz Labs Partner Program. Our team evaluates every partner profile within 24–48 business hours.
    </p>
</div>

<div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.75rem;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.85rem;">
        <span style="color: var(--text-muted);">Assigned Referral Code:</span>
        <span style="font-family: monospace; font-weight: bold; color: #34d399;">{{ $partner?->referral_code ?? 'Pending' }}</span>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.85rem;">
        <span style="color: var(--text-muted);">Standard Commission Rate:</span>
        <span style="font-weight: 600; color: #fff;">{{ $partner?->commission_rate ?? '20.00' }}%</span>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
        <span style="color: var(--text-muted);">Registered Email:</span>
        <span style="font-weight: 500; color: #fff;">{{ auth()->user()->email }}</span>
    </div>
</div>

<div style="display: flex; gap: 0.75rem; justify-content: center;">
    <a href="{{ route('contact') }}" class="auth-btn" style="text-align: center; text-decoration: none; padding: 0.65rem 1.25rem; font-size: 0.85rem;">
        Contact Team
    </a>

    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
        @csrf
        <button type="submit" class="auth-btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.15); color: var(--text-muted); padding: 0.65rem 1.25rem; font-size: 0.85rem;">
            Sign Out
        </button>
    </form>
</div>
@endsection
