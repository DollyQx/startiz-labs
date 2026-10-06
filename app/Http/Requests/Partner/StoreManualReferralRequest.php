<?php

namespace App\Http\Requests\Partner;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreManualReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->isPartner()
            && auth()->user()->partnerProfile?->isApproved();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => $this->company_name ?? $this->business_name,
            'contact_name' => $this->contact_name ?? $this->client_name,
            'email' => $this->email ? strtolower(trim((string) $this->email)) : ($this->client_email ? strtolower(trim((string) $this->client_email)) : null),
            'phone' => $this->phone ? trim((string) $this->phone) : ($this->client_phone ? trim((string) $this->client_phone) : null),
            'requirement' => $this->requirement ?? $this->service_requested,
            'notes' => $this->notes ?? $this->additional_notes,
        ]);
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'min:2', 'max:255'],
            'contact_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:25', 'regex:/^[+]?[0-9\s\-().]{7,25}$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'requirement' => ['required', 'string', 'min:5', 'max:2000'],
            'estimated_budget' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Business or company name is required.',
            'contact_name.required' => 'Contact person name is required.',
            'phone.required' => 'Contact phone number is required.',
            'phone.regex' => 'The phone number format is invalid.',
            'email.required' => 'Contact email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'requirement.required' => 'Business requirement or project description is required.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $partner = $user?->partnerProfile;

            if (! $partner) {
                $validator->errors()->add('partner', 'Partner profile not found.');
                return;
            }

            $email = strtolower(trim((string) $this->input('email')));
            $phone = trim((string) $this->input('phone'));

            // 1. Self-referral prevention: Email
            if ($email && $email === strtolower(trim($user->email))) {
                $validator->errors()->add('email', 'Self-referrals are not permitted. You cannot submit yourself as a referral.');
                return;
            }

            // Self-referral prevention: Phone
            $cleanPhone = preg_replace('/\D/', '', $phone);
            if ($cleanPhone !== '') {
                $partnerPhone = preg_replace('/\D/', '', (string) ($partner->phone ?? $user->phone ?? ''));
                if ($partnerPhone !== '' && $cleanPhone === $partnerPhone) {
                    $validator->errors()->add('phone', 'Self-referrals are not permitted. You cannot submit your own phone number.');
                    return;
                }
            }

            // 2. Duplicate referral protection: Email
            if ($email && Referral::where('client_email', $email)->exists()) {
                $validator->errors()->add('email', 'A referral for this contact email address already exists in the system.');
                return;
            }

            // Duplicate referral protection: Existing User linked to a referral
            $existingUser = $email ? User::where('email', $email)->first() : null;
            if ($existingUser) {
                if ($existingUser->id === $user->id) {
                    $validator->errors()->add('email', 'Self-referrals are not permitted. You cannot submit yourself as a referral.');
                    return;
                }
                if (Referral::where('client_id', $existingUser->id)->exists()) {
                    $validator->errors()->add('email', 'This client account is already associated with an existing referral.');
                    return;
                }
            }

            // Duplicate referral protection: Phone
            if ($phone !== '' && Referral::where('client_phone', $phone)->exists()) {
                $validator->errors()->add('phone', 'A referral with this contact phone number already exists in the system.');
                return;
            }
        });
    }
}
