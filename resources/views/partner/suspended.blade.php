@extends('layouts.auth')

@section('title', 'Partner Account Inactive — Partner Program')

@section('content')
<div style="text-align: center; margin-bottom: 2rem;">
    <div style="width: 64px; height: 64px; margin: 0 auto 1.25rem; background: rgba(239, 68, 68, 0.15); border: 2px solid rgba(239, 68, 68, 0.3); border-radius: 50%; display: flex; items-center; justify-content: center;">
        <svg style="width: 32px; height: 32px; color: #ef4444; margin: auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>

    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: #f87171; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
        Account Suspended
    </span>

    <h2 class="auth-title" style="margin-bottom: 0.5rem;">Partner Account Suspended</h2>
    <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 380px; margin: 0 auto; line-height: 1.5;">
        Your partner portal access is temporarily inactive. Please reach out to Startiz Labs partner compliance team.
    </p>
</div>

<div style="display: flex; gap: 0.75rem; justify-content: center;">
    <a href="{{ route('contact') }}" class="auth-btn" style="text-align: center; text-decoration: none; padding: 0.65rem 1.25rem; font-size: 0.85rem;">
        Contact Support
    </a>

    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
        @csrf
        <button type="submit" class="auth-btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.15); color: var(--text-muted); padding: 0.65rem 1.25rem; font-size: 0.85rem;">
            Sign Out
        </button>
    </form>
</div>
@endsection
