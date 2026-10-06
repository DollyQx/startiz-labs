<?php

namespace Tests\Feature\Partner;

use App\Enums\PartnerStatus;
use App\Enums\ReferralStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Commission;
use App\Models\Lead;
use App\Models\PartnerProfile;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PartnerResourcesAndReferralSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $partnerUser;
    protected PartnerProfile $partnerProfile;
    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Controller',
            'email' => 'admin@startizlabs.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->partnerUser = User::create([
            'name' => 'Alpha Partner',
            'email' => 'alpha@startizlabs.com',
            'phone' => '+91 9876500001',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->partnerProfile = PartnerProfile::create([
            'user_id' => $this->partnerUser->id,
            'referral_code' => 'STZ-ALPHA1',
            'company_name' => 'Alpha Growth Solutions',
            'phone' => '+91 9876500001',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'bank_transfer',
            'payout_details' => 'HDFC Bank - 5010012345678',
            'approved_at' => now(),
            'approved_by_id' => $this->admin->id,
        ]);

        $this->clientUser = User::create([
            'name' => 'Client User',
            'email' => 'client@startizlabs.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    // ==========================================
    // Partner Resources Tests
    // ==========================================

    public function test_guest_is_redirected_from_partner_resources(): void
    {
        $response = $this->get(route('partner.resources'));
        $response->assertRedirect();
    }

    public function test_approved_partner_can_access_resources_page(): void
    {
        $response = $this->actingAs($this->partnerUser)->get(route('partner.resources'));

        $response->assertOk();
        $response->assertViewIs('partner.resources');
    }

    public function test_client_is_denied_from_partner_resources(): void
    {
        $response = $this->actingAs($this->clientUser)->get(route('partner.resources'));
        $response->assertForbidden();
    }

    public function test_resource_page_contains_expected_content_messages_and_services(): void
    {
        $response = $this->actingAs($this->partnerUser)->get(route('partner.resources'));

        $response->assertOk();
        // Check Partner Introduction
        $response->assertSee('How to Approach Businesses & Leads');
        $response->assertSee('Spot the Opportunity');

        // Check Ready-to-Use Messages
        $response->assertSee('WhatsApp');
        $response->assertSee('Telegram');
        $response->assertSee('LinkedIn');
        $response->assertSee('Copy Message');
        $response->assertSee('If your business needs a website, web application, mobile app');

        // Check Services to Refer
        $response->assertSee('Website Development');
        $response->assertSee('Web Applications');
        $response->assertSee('Mobile Applications');
        $response->assertSee('E-commerce');
        $response->assertSee('Custom Software');
        $response->assertSee('Cybersecurity');
        $response->assertSee('Other Digital Solutions');

        // Check Submit a Referral CTA
        $response->assertSee('Submit a Referral');
        $response->assertSee(route('partner.referrals.create'));
    }

    public function test_resource_page_displays_dynamic_partner_referral_link(): void
    {
        $response = $this->actingAs($this->partnerUser)->get(route('partner.resources'));

        $response->assertOk();
        $response->assertSee('STZ-ALPHA1');
        $response->assertSee($this->partnerProfile->referralUrl());
    }

    // ==========================================
    // Manual Referral Submission Tests
    // ==========================================

    public function test_get_referral_creation_form_returns_200(): void
    {
        $response = $this->actingAs($this->partnerUser)->get(route('partner.referrals.create'));

        $response->assertOk();
        $response->assertViewIs('partner.referrals.create');
        $response->assertSee('Submit a Direct Referral');
        $response->assertSee('Business / Company Name');
        $response->assertSee('Contact Person Name');
        $response->assertSee('Email Address');
        $response->assertSee('Phone Number');
        $response->assertSee('Business Requirement / Project Description');
    }

    public function test_valid_submission_creates_referral_and_lead(): void
    {
        $payload = [
            'company_name' => 'Acme Technologies',
            'contact_name' => 'John Doe',
            'email' => 'john.doe@acmetech.com',
            'phone' => '+91 9123456789',
            'requirement' => 'Need a custom B2B inventory tracking web application with ERP integration.',
            'estimated_budget' => '₹1,50,000 - ₹2,50,000',
            'notes' => 'Preferred consultation on weekdays after 3 PM.',
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $response->assertRedirect(route('partner.referrals.index'));
        $response->assertSessionHas('status', 'Referral submitted successfully.');

        // Assert Referral created
        $referral = Referral::where('client_email', 'john.doe@acmetech.com')->first();
        $this->assertNotNull($referral);
        $this->assertEquals($this->partnerProfile->id, $referral->partner_id);
        $this->assertEquals('STZ-ALPHA1', $referral->referral_code);
        $this->assertEquals('Acme Technologies', $referral->company_name);
        $this->assertEquals('John Doe', $referral->client_name);
        $this->assertEquals('+91 9123456789', $referral->client_phone);
        $this->assertEquals(ReferralStatus::NEW, $referral->status);
        $this->assertEquals('₹1,50,000 - ₹2,50,000', $referral->estimated_budget);
        $this->assertStringStartsWith('STZ-REF', $referral->reference_number);

        // Assert Lead created and linked
        $this->assertNotNull($referral->lead_id);
        $lead = Lead::find($referral->lead_id);
        $this->assertNotNull($lead);
        $this->assertEquals('Acme Technologies', $lead->company_name);
        $this->assertEquals('John Doe', $lead->name);
        $this->assertEquals('partner_referral', $lead->source);
    }

    public function test_required_validation_rules_are_enforced(): void
    {
        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), []);

        $response->assertSessionHasErrors([
            'company_name',
            'contact_name',
            'email',
            'phone',
            'requirement',
        ]);
        $this->assertEquals(0, Referral::count());
    }

    public function test_invalid_email_is_rejected(): void
    {
        $payload = [
            'company_name' => 'Acme Corp',
            'contact_name' => 'John Doe',
            'email' => 'invalid-not-an-email',
            'phone' => '+91 9123456789',
            'requirement' => 'Need a mobile app for booking.',
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, Referral::count());
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $payload = [
            'company_name' => 'Acme Corp',
            'contact_name' => 'John Doe',
            'email' => 'john@acme.com',
            'phone' => 'call-me-now!',
            'requirement' => 'Need a mobile app for booking.',
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $response->assertSessionHasErrors('phone');
        $this->assertEquals(0, Referral::count());
    }

    public function test_self_referral_is_rejected(): void
    {
        // Attempt using partner's own email
        $payload = [
            'company_name' => 'My Own Venture',
            'contact_name' => 'Alpha Partner',
            'email' => 'alpha@startizlabs.com',
            'phone' => '+91 9876543210',
            'requirement' => 'Trying to refer myself.',
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, Referral::count());

        // Attempt using partner's own phone number
        $payloadPhone = [
            'company_name' => 'Another Venture',
            'contact_name' => 'Someone Else',
            'email' => 'someone@otherventure.com',
            'phone' => '+91 9876500001', // Partner's phone
            'requirement' => 'Trying to use own phone.',
        ];

        $responsePhone = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payloadPhone);

        $responsePhone->assertSessionHasErrors('phone');
        $this->assertEquals(0, Referral::count());
    }

    public function test_duplicate_referral_protection_rejects_duplicate(): void
    {
        // Create an existing referral
        Referral::create([
            'partner_id' => $this->partnerProfile->id,
            'referral_code' => 'STZ-ALPHA1',
            'client_name' => 'Existing Contact',
            'client_email' => 'existing@clientdomain.com',
            'client_phone' => '+91 9876543210',
            'service_requested' => 'Web App',
            'status' => ReferralStatus::NEW,
        ]);

        $this->assertEquals(1, Referral::count());

        // Attempt to submit referral with identical email
        $payload = [
            'company_name' => 'Duplicate Attempt Inc',
            'contact_name' => 'Duplicate Contact',
            'email' => 'existing@clientdomain.com',
            'phone' => '+91 9999911111',
            'requirement' => 'Trying to submit duplicate referral.',
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, Referral::count(), 'Duplicate referral must not be created.');
    }

    public function test_partner_ownership_enforced_and_cannot_spoof_partner_id(): void
    {
        $partnerBUser = User::create([
            'name' => 'Beta Partner',
            'email' => 'beta@startizlabs.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $partnerBProfile = PartnerProfile::create([
            'user_id' => $partnerBUser->id,
            'referral_code' => 'STZ-BETA01',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'upi',
            'payout_details' => 'beta@upi',
        ]);

        // Partner Alpha submits a referral but passes partner_id of Partner Beta
        $payload = [
            'partner_id' => $partnerBProfile->id,
            'referral_code' => 'STZ-BETA01',
            'company_name' => 'Alpha Client Company',
            'contact_name' => 'Alice Smith',
            'email' => 'alice@alphaclient.com',
            'phone' => '+91 9112233445',
            'requirement' => 'Website redesign.',
        ];

        $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $referral = Referral::where('client_email', 'alice@alphaclient.com')->first();
        $this->assertNotNull($referral);
        // Ownership MUST be derived from authenticated user, NOT the spoofed payload
        $this->assertEquals($this->partnerProfile->id, $referral->partner_id);
        $this->assertEquals('STZ-ALPHA1', $referral->referral_code);
    }

    public function test_partner_a_cannot_access_partner_b_referral(): void
    {
        $partnerBUser = User::create([
            'name' => 'Partner Bravo',
            'email' => 'bravo@partnerdomain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $partnerBProfile = PartnerProfile::create([
            'user_id' => $partnerBUser->id,
            'referral_code' => 'STZ-BRAVO1',
            'commission_rate' => 25.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'upi',
            'payout_details' => 'bravo@upi',
        ]);

        $referralB = Referral::create([
            'partner_id' => $partnerBProfile->id,
            'referral_code' => 'STZ-BRAVO1',
            'company_name' => 'Bravo Secret Client',
            'client_name' => 'Secret Bravo Contact',
            'client_email' => 'secret@bravoclient.com',
            'status' => ReferralStatus::NEW,
        ]);

        // Partner Alpha tries to access Partner Bravo's referral -> 403 Forbidden
        $response = $this->actingAs($this->partnerUser)
            ->get(route('partner.referrals.show', $referralB));

        $response->assertForbidden();
    }

    public function test_commission_is_not_created_on_referral_submission(): void
    {
        $payload = [
            'company_name' => 'Global Logistics Corp',
            'contact_name' => 'Robert Johnson',
            'email' => 'robert@globallogistics.com',
            'phone' => '+91 9876543219',
            'requirement' => 'Fleet management web platform.',
            'estimated_budget' => '₹3,00,000',
        ];

        $this->actingAs($this->partnerUser)
            ->post(route('partner.referrals.store'), $payload);

        $referral = Referral::where('client_email', 'robert@globallogistics.com')->first();
        $this->assertNotNull($referral);

        // Crucial requirement: Commission must NOT be created at submission time
        $this->assertEquals(0, Commission::count());
        $this->assertEquals(0, $referral->commissions()->count());
    }

    public function test_admin_can_see_submitted_referral_in_admin_crm(): void
    {
        $referral = Referral::create([
            'partner_id' => $this->partnerProfile->id,
            'referral_code' => 'STZ-ALPHA1',
            'company_name' => 'Apex Healthcare Solutions',
            'client_name' => 'Dr. Marcus Welby',
            'client_email' => 'marcus@apexhealthcare.com',
            'client_phone' => '+91 9988776655',
            'service_requested' => 'Clinic Management Portal',
            'estimated_budget' => '₹2,00,000',
            'status' => ReferralStatus::NEW,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.partners.referrals'));

        $response->assertOk();
        $response->assertSee('Apex Healthcare Solutions');
        $response->assertSee('Dr. Marcus Welby');
        $response->assertSee('marcus@apexhealthcare.com');
        $response->assertSee('Clinic Management Portal');
        $response->assertSee('Budget: ₹2,00,000');
        $response->assertSee('Alpha Partner');
    }

    public function test_unauthorized_roles_cannot_submit_referral(): void
    {
        $payload = [
            'company_name' => 'Unauthorized Attempt',
            'contact_name' => 'Hacker',
            'email' => 'hacker@example.com',
            'phone' => '+91 9999999999',
            'requirement' => 'Unauthorized request.',
        ];

        // Guest cannot access create form or submit
        $guestGet = $this->get(route('partner.referrals.create'));
        $guestGet->assertRedirect();

        $guestPost = $this->post(route('partner.referrals.store'), $payload);
        $guestPost->assertRedirect();

        // Client cannot access create form or submit
        $clientGet = $this->actingAs($this->clientUser)->get(route('partner.referrals.create'));
        $clientGet->assertForbidden();

        $clientPost = $this->actingAs($this->clientUser)->post(route('partner.referrals.store'), $payload);
        $clientPost->assertForbidden();

        $this->assertEquals(0, Referral::count());
    }
}
