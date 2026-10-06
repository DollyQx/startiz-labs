<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $partner = $user->partnerProfile;

        return view('partner.profile', compact('user', 'partner'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $partner = $user->partnerProfile;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'payout_method' => ['required', 'string', 'in:upi,bank_transfer'],
            'payout_details' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Security: Partner cannot update commission_rate, referral_code, or status
        $partner->update([
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'website' => $request->website,
            'payout_method' => $request->payout_method,
            'payout_details' => $request->payout_details,
        ]);

        return back()->with('status', 'Profile and payout preferences updated successfully.');
    }
}
