<?php

namespace Tests\Feature\Public;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_partners_landing_page_returns_successful_http_status_200(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
    }

    public function test_page_contains_required_partner_program_hero_content(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Earn 20% Commission by Referring Clients to Startiz Labs');
        $response->assertSee('Refer businesses that need websites, applications, software or digital solutions. When your referred client becomes a qualifying customer, earn commission through the Startiz Labs Partner Program.');
        $response->assertSee('Official Startiz Labs Partner Program');
    }

    public function test_hero_and_final_ctas_link_to_partner_registration(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Join as a Partner');
        $response->assertSee(route('partner.register'));
    }

    public function test_hero_and_footer_ctas_link_to_partner_login(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Partner Login');
        $response->assertSee(route('partner.login'));
    }

    public function test_how_it_works_section_contains_all_four_steps(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('How It Works');
        $response->assertSee('Join');
        $response->assertSee('Create your free Startiz Labs Partner account.');
        $response->assertSee('Refer');
        $response->assertSee('Share your unique referral link with businesses.');
        $response->assertSee('Convert');
        $response->assertSee('Your referred client contacts Startiz Labs and becomes a qualifying customer.');
        $response->assertSee('Earn');
        $response->assertSee('Receive the applicable commission through the Partner Portal.');
    }

    public function test_twenty_percent_commission_section_and_marketing_example(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('20%');
        $response->assertSee('Project Value:');
        $response->assertSee('₹50,000');
        $response->assertSee('₹10,000');
        $response->assertSee('Commission:');
    }

    public function test_what_can_you_refer_section_displays_all_required_service_categories(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('What Can You Refer?');
        $response->assertSee('Website Development');
        $response->assertSee('Web Applications');
        $response->assertSee('Mobile Applications');
        $response->assertSee('E-commerce');
        $response->assertSee('Custom Software');
        $response->assertSee('Cybersecurity');
        $response->assertSee('Other Digital Solutions');
    }

    public function test_partner_benefits_section_displays_all_required_tooling(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Partner Benefits');
        $response->assertSee('Unique Referral Link');
        $response->assertSee('Referral Tracking');
        $response->assertSee('Partner Dashboard');
        $response->assertSee('Commission Tracking');
        $response->assertSee('Leaderboard');
        $response->assertSee('Payout Management');
        $response->assertSee('Transparent Referral Status');
    }

    public function test_payout_section_explains_portal_rules_without_guaranteed_claims(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('How Commissions & Payouts Are Handled', false);
        $response->assertSee('Partner Portal');
        $response->assertDontSee('Instant payout guaranteed');
        $response->assertDontSee('Instant payouts');
    }

    public function test_faq_section_contains_all_seven_required_questions(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('How does the referral program work?');
        $response->assertSee('How much commission can I earn?');
        $response->assertSee('How do I track referrals?');
        $response->assertSee('When does a commission become payable?');
        $response->assertSee('What happens if a client cancels/refunds?');
        $response->assertSee('Can I refer myself?');
        $response->assertSee('How are payouts processed?');
    }

    public function test_terms_trust_notice_contains_exact_disclaimer_and_link(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Partner commissions are subject to the Startiz Labs Partner Terms & Conditions, referral attribution rules, qualifying payment conditions and anti-fraud policies.', false);
        $response->assertSee(route('partner.terms'));
    }

    public function test_final_cta_section_renders_correctly(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('Start earning with Startiz Labs');
    }

    public function test_seo_metadata_is_correctly_configured(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
        $response->assertSee('<title>Startiz Labs Partner Program | Earn 20% Commission</title>', false);
        $response->assertSee('Join the Startiz Labs Partner Program and earn 20% commission', false);
    }

    public function test_partner_terms_page_returns_successful_http_status_200(): void
    {
        $response = $this->get('/partner-terms');

        $response->assertStatus(200);
        $response->assertSee('Partner Program Terms & Conditions', false);
        $response->assertSee('Referral Attribution & Tracking Window', false);
        $response->assertSee('Strict Anti-Fraud Policy', false);
    }

    public function test_authenticated_client_can_access_partners_landing_page(): void
    {
        $client = User::create([
            'name' => 'Authed Client',
            'email' => 'client_landing@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->actingAs($client)->get('/partners');
        $response->assertStatus(200);
    }

    public function test_authenticated_partner_can_access_partners_landing_page(): void
    {
        $partner = User::create([
            'name' => 'Authed Partner',
            'email' => 'partner_landing@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->actingAs($partner)->get('/partners');
        $response->assertStatus(200);
    }
}
