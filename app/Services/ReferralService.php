<?php

namespace App\Services;

use App\Enums\PartnerStatus;
use App\Enums\ReferralStatus;
use App\Models\Lead;
use App\Models\PartnerProfile;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralService
{
    /**
     * Session and cookie keys for referral attribution.
     */
    public const COOKIE_KEY = 'stz_ref_code';
    public const SESSION_KEY = 'stz_ref_code';

    /**
     * Capture referral code from incoming request into session/cookie if valid.
     */
    public function captureFromRequest(Request $request): ?string
    {
        $code = $request->query('ref');

        if (empty($code)) {
            return null;
        }

        $code = strtoupper(trim($code));

        $partner = PartnerProfile::where('referral_code', $code)
            ->where('status', PartnerStatus::APPROVED->value)
            ->first();

        if (! $partner) {
            return null;
        }

        // Prevent partner from capturing their own code if logged in
        if ($request->user() && $request->user()->id === $partner->user_id) {
            return null;
        }

        session([self::SESSION_KEY => $code]);
        session(['startiz_referral_code' => $code]);

        return $code;
    }

    /**
     * Resolve the active referral code from request, session, or cookie.
     */
    public function getActiveReferralCode(Request $request): ?string
    {
        $code = $request->input('referral_code')
            ?? $request->query('ref')
            ?? session(self::SESSION_KEY)
            ?? session('startiz_referral_code')
            ?? $request->cookie(self::COOKIE_KEY)
            ?? $request->cookie('stz_ref');

        if (! $code) {
            return null;
        }

        return strtoupper(trim((string) $code));
    }

    /**
     * Resolve the active approved partner from request context.
     */
    public function getAttributedPartner(Request $request): ?PartnerProfile
    {
        $code = $this->getActiveReferralCode($request);

        if (! $code) {
            return null;
        }

        return PartnerProfile::with('user')
            ->where('referral_code', $code)
            ->where('status', PartnerStatus::APPROVED->value)
            ->first();
    }

    /**
     * Safely attribute a referral to a partner with strict validations:
     * - Approved partner check
     * - Self-referral prevention
     * - Duplicate attribution prevention
     */
    public function attributeReferral(
        string $referralCode,
        array $clientData,
        ?User $clientUser = null,
        ?Lead $lead = null,
        ReferralStatus $status = ReferralStatus::NEW
    ): ?Referral {
        $referralCode = strtoupper(trim($referralCode));

        $partner = PartnerProfile::with('user')
            ->where('referral_code', $referralCode)
            ->where('status', PartnerStatus::APPROVED->value)
            ->first();

        if (! $partner) {
            return null;
        }

        $email = isset($clientData['email']) ? strtolower(trim($clientData['email'])) : null;
        if (! $email && $clientUser) {
            $email = strtolower(trim($clientUser->email));
        }

        // Safety 1: Prevent self-referral
        if ($clientUser && $clientUser->id === $partner->user_id) {
            return null;
        }

        if ($email && strtolower($partner->user->email) === $email) {
            return null;
        }

        // Safety 2: Prevent duplicate attribution for the same client user or email
        if ($clientUser) {
            $existing = Referral::where('client_id', $clientUser->id)->first();
            if ($existing) {
                return $existing;
            }
        }

        if ($email) {
            $existing = Referral::where('client_email', $email)->first();
            if ($existing) {
                // If existing referral was unattached to client user, link it
                if ($clientUser && ! $existing->client_id) {
                    $existing->update([
                        'client_id' => $clientUser->id,
                        'status' => ReferralStatus::CONVERTED,
                        'converted_at' => $existing->converted_at ?? now(),
                    ]);
                }
                return $existing;
            }
        }

        // Create new attributed referral
        $referral = Referral::create([
            'partner_id' => $partner->id,
            'referral_code' => $partner->referral_code,
            'client_id' => $clientUser?->id,
            'lead_id' => $lead?->id,
            'client_name' => $clientData['name'] ?? $clientUser?->name ?? 'Lead',
            'client_email' => $email,
            'client_phone' => $clientData['phone'] ?? $clientUser?->phone,
            'service_requested' => $clientData['service_requested'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
            'converted_at' => ($status === ReferralStatus::CONVERTED) ? now() : null,
            'notes' => $clientData['notes'] ?? null,
        ]);

        // Clear session referral attribution once consumed
        session()->forget(self::SESSION_KEY);

        return $referral;
    }
}
