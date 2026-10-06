<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PartnerStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PartnerRegisterController extends Controller
{
    /**
     * Display partner registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.partner-register');
    }

    /**
     * Handle incoming partner registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'payout_method' => ['nullable', 'string', 'in:upi,bank_transfer'],
            'payout_details' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
            'last_login_at' => now(),
        ]);

        PartnerProfile::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'website' => $request->website,
            'payout_method' => $request->payout_method ?? 'upi',
            'payout_details' => $request->payout_details,
            'commission_rate' => (float) config('partner.default_commission_rate', 20.00),
            'status' => PartnerStatus::PENDING,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('partner.pending')
            ->with('status', 'Your partner application has been submitted successfully! Our team will review your application shortly.');
    }
}
