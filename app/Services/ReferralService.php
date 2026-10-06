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

    /**
     * Submit a manual referral from an approved partner with strict validation:
     * - Approved partner check
     * - Self-referral prevention (email and phone)
     * - Duplicate attribution prevention
     * - Linked Lead and Referral creation
     * - Initial status is NEW (no commission created)
     */
    public function submitManualReferral(PartnerProfile $partner, array $data): Referral
    {
        if (! $partner->isApproved()) {
            throw new \DomainException('Only approved partners can submit referrals.');
        }

        $companyName = trim($data['company_name'] ?? $data['business_name'] ?? '');
        $contactName = trim($data['contact_name'] ?? $data['client_name'] ?? '');
        $email = strtolower(trim($data['email'] ?? $data['client_email'] ?? ''));
        $phone = trim($data['phone'] ?? $data['client_phone'] ?? '');
        $requirement = trim($data['requirement'] ?? $data['service_requested'] ?? '');
        $budget = trim($data['estimated_budget'] ?? '');
        $notes = trim($data['notes'] ?? $data['additional_notes'] ?? '');

        // 1. Prevent self-referral (email)
        if ($email && strtolower(trim($partner->user->email)) === $email) {
            throw new \DomainException('Self-referrals are not permitted. You cannot refer yourself.');
        }

        // Prevent self-referral (phone)
        $cleanPhone = preg_replace('/\D/', '', $phone);
        if ($cleanPhone !== '') {
            $partnerPhone = preg_replace('/\D/', '', (string) ($partner->phone ?? $partner->user->phone ?? ''));
            if ($partnerPhone !== '' && $cleanPhone === $partnerPhone) {
                throw new \DomainException('Self-referrals are not permitted. You cannot refer yourself.');
            }
        }

        // 2. Prevent duplicate referral (email)
        if ($email && Referral::where('client_email', $email)->exists()) {
            throw new \DomainException('A referral with this contact email already exists in the system.');
        }

        // Prevent duplicate referral (client user)
        $existingUser = $email ? User::where('email', $email)->first() : null;
        if ($existingUser) {
            if ($existingUser->id === $partner->user_id) {
                throw new \DomainException('Self-referrals are not permitted. You cannot refer yourself.');
            }
            if (Referral::where('client_id', $existingUser->id)->exists()) {
                throw new \DomainException('A referral for this client already exists in the system.');
            }
        }

        // Prevent duplicate referral (phone)
        if ($phone !== '' && Referral::where('client_phone', $phone)->exists()) {
            throw new \DomainException('A referral with this contact phone number already exists in the system.');
        }

        // 3. Create Lead in CRM pipeline
        $leadNotes = "Manual referral submitted by partner: {$partner->user->name} ({$partner->referral_code})\n"
            . ($requirement ? "Requirement: {$requirement}\n" : '')
            . ($budget ? "Estimated Budget: {$budget}\n" : '')
            . ($notes ? "Notes: {$notes}\n" : '');

        $lead = Lead::create([
            'name' => $contactName,
            'email' => $email,
            'phone' => $phone,
            'company_name' => $companyName,
            'source' => 'partner_referral',
            'status' => \App\Enums\LeadStatus::NEW,
            'client_id' => $existingUser?->id,
            'notes' => trim($leadNotes),
        ]);

        // 4. Create Referral
        $compiledNotes = trim(($notes ? "{$notes}\n" : '') . ($budget ? "Estimated Budget: {$budget}" : ''));

        return Referral::create([
            'partner_id' => $partner->id,
            'referral_code' => $partner->referral_code,
            'client_id' => $existingUser?->id,
            'lead_id' => $lead->id,
            'client_name' => $contactName,
            'company_name' => $companyName,
            'client_email' => $email,
            'client_phone' => $phone,
            'service_requested' => $requirement,
            'estimated_budget' => $budget ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => ReferralStatus::NEW,
            'notes' => $compiledNotes ?: null,
        ]);
    }
}
