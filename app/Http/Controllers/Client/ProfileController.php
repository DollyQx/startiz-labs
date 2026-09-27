<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the authenticated client's profile details.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $user->clientProfile ?? ClientProfile::firstOrCreate(['user_id' => $user->id]);

        Gate::authorize('view', $profile);

        return view('client.profile', compact('user', 'profile'));
    }

    /**
     * Update the authenticated client's profile details.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->clientProfile ?? ClientProfile::firstOrCreate(['user_id' => $user->id]);

        Gate::authorize('update', $profile);

        $validatedUser = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $validatedProfile = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'gst_vat_number' => ['nullable', 'string', 'max:50'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'industry' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $user->update($validatedUser);
        $profile->update($validatedProfile);

        return back()->with('status', 'Profile information updated successfully!');
    }
}
