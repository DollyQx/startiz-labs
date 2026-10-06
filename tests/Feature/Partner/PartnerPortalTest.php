<?php

namespace Tests\Feature\Partner;

use App\Enums\CommissionStatus;
use App\Enums\PartnerStatus;
use App\Enums\PayoutStatus;
use App\Enums\ReferralStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Commission;
use App\Models\PartnerProfile;
use App\Models\Payout;
use App\Models\Referral;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\LeaderboardService;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PartnerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $partnerUser;
    protected PartnerProfile $partnerProfile;

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
            'name' => 'Top Partner',
            'email' => 'partner@startizlabs.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->partnerProfile = PartnerProfile::create([
            'user_id' => $this->partnerUser->id,
            'referral_code' => 'STZ-000001',
            'company_name' => 'Growth Agency Ltd',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'bank_transfer',
            'payout_details' => 'HDFC Bank - 5010012345678 - HDFC0000123',
            'approved_at' => now(),
            'approved_by_id' => $this->admin->id,
        ]);
    }

    public function test_partner_registration_creates_pending_partner_and_profile(): void
    {
        $response = $this->post('/partner/register', [
            'name' => 'New Affiliate',
            'email' => 'affiliate@partnerdomain.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'phone' => '+91 9876543210',
            'company_name' => 'Alpha Partners',
            'website' => 'https://alphapartners.com',
            'payout_method' => 'upi',
            'payout_details' => 'alpha@okaxis',
        ]);

        $response->assertRedirect(route('partner.pending'));

        $user = User::where('email', 'affiliate@partnerdomain.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isPartner());
        $this->assertEquals(UserRole::PARTNER, $user->role);

        $profile = $user->partnerProfile;
        $this->assertNotNull($profile);
        $this->assertEquals(PartnerStatus::PENDING, $profile->status);
        $this->assertEquals(20.00, (float) $profile->commission_rate);
        $this->assertStringStartsWith('STZ-', $profile->referral_code);
    }

    public function test_pending_partner_cannot_access_protected_dashboard(): void
    {
        $pendingUser = User::create([
            'name' => 'Pending Partner',
            'email' => 'pending@partnerdomain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $pendingProfile = PartnerProfile::create([
            'user_id' => $pendingUser->id,
            'referral_code' => 'STZ-999999',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::PENDING,
            'payout_method' => 'upi',
            'payout_details' => 'test@upi',
        ]);

        $response = $this->actingAs($pendingUser)->get('/partner/dashboard');
        $response->assertRedirect(route('partner.pending'));

        $pendingViewResponse = $this->actingAs($pendingUser)->get('/partner/pending');
        $pendingViewResponse->assertStatus(200);
        $pendingViewResponse->assertSee('Partner Application Under Review');
    }

    public function test_admin_can_approve_partner_and_grant_access(): void
    {
        $pendingUser = User::create([
            'name' => 'Candidate Partner',
            'email' => 'candidate@partnerdomain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $pendingProfile = PartnerProfile::create([
            'user_id' => $pendingUser->id,
            'referral_code' => 'STZ-777777',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::PENDING,
            'payout_method' => 'upi',
            'payout_details' => 'candidate@upi',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.partners.status', $pendingProfile), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $pendingProfile->refresh();
        $this->assertEquals(PartnerStatus::APPROVED, $pendingProfile->status);
        $this->assertEquals($this->admin->id, $pendingProfile->approved_by_id);

        $dashResponse = $this->actingAs($pendingUser)->get('/partner/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Candidate Partner');
        $dashResponse->assertSee('Partner Account Approved');
        $dashResponse->assertSee('Commission:');
        $dashResponse->assertSee('20%');
        $dashResponse->assertSee('How to earn:');
        $dashResponse->assertSee('Share your link');
        $dashResponse->assertSee('Bring a business lead');
        $dashResponse->assertSee('Startiz Labs closes the project');
        $dashResponse->assertSee('You earn eligible commission');
        $dashResponse->assertSee('Copy Link');
        $dashResponse->assertSee('Share on WhatsApp');

        // Verify in-app notification was dispatched to partner
        $this->assertCount(1, $pendingUser->fresh()->notifications);
        $this->assertEquals('🎉 Partner Account Approved', $pendingUser->fresh()->notifications->first()->data['title']);
    }

    public function test_partner_cannot_access_admin_or_client_routes(): void
    {
        $adminAttempt = $this->actingAs($this->partnerUser)->get('/admin/dashboard');
        $adminAttempt->assertStatus(403);

        $clientAttempt = $this->actingAs($this->partnerUser)->get('/client/dashboard');
        $clientAttempt->assertStatus(403);
    }

    public function test_client_cannot_access_partner_routes(): void
    {
        $clientUser = User::create([
            'name' => 'Client User',
            'email' => 'client@clientdomain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $partnerAttempt = $this->actingAs($clientUser)->get('/partner/dashboard');
        $partnerAttempt->assertStatus(403);
    }

    public function test_referral_attribution_on_client_registration(): void
    {
        $response = $this->withSession(['startiz_referral_code' => 'STZ-000001'])
            ->post('/register', [
                'name' => 'Referred Client',
                'email' => 'referred@clientdomain.com',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
                'phone' => '+91 9999988888',
                'terms' => '1',
            ]);

        $response->assertRedirect(route('client.dashboard'));

        $clientUser = User::where('email', 'referred@clientdomain.com')->first();
        $this->assertNotNull($clientUser);

        $referral = Referral::where('partner_id', $this->partnerProfile->id)
            ->where('client_id', $clientUser->id)
            ->first();

        $this->assertNotNull($referral);
        $this->assertEquals(ReferralStatus::CONVERTED, $referral->status);
        $this->assertEquals('STZ-000001', $referral->referral_code);
    }

    public function test_duplicate_referral_prevention_and_self_referral_rejection(): void
    {
        $referralService = app(ReferralService::class);

        // Self-referral prevention
        $selfAttribution = $referralService->attributeReferral(
            'STZ-000001',
            ['name' => 'Top Partner', 'email' => 'partner@startizlabs.com'],
            $this->partnerUser
        );
        $this->assertNull($selfAttribution, 'Self referrals must not be permitted.');

        // Normal attribution
        $firstClient = User::create([
            'name' => 'First Client',
            'email' => 'first@domain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $firstAttribution = $referralService->attributeReferral(
            'STZ-000001',
            ['name' => 'First Client', 'email' => 'first@domain.com'],
            $firstClient
        );
        $this->assertNotNull($firstAttribution);

        // Duplicate attribution with other code should return existing referral without duplicating
        $anotherPartner = PartnerProfile::create([
            'user_id' => $this->admin->id,
            'referral_code' => 'STZ-000002',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'upi',
            'payout_details' => 'second@upi',
        ]);

        $duplicateAttempt = $referralService->attributeReferral(
            'STZ-000002',
            ['name' => 'First Client', 'email' => 'first@domain.com'],
            $firstClient
        );

        $this->assertEquals($firstAttribution->id, $duplicateAttempt->id);
        $this->assertEquals($this->partnerProfile->id, $duplicateAttempt->partner_id);
    }

    public function test_partner_data_isolation_between_partners(): void
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
            'referral_code' => 'STZ-000002',
            'commission_rate' => 25.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'upi',
            'payout_details' => 'bravo@upi',
        ]);

        $referralB = Referral::create([
            'partner_id' => $partnerBProfile->id,
            'referral_code' => 'STZ-000002',
            'client_name' => 'Secret Bravo Client',
            'client_email' => 'secret@bravoclient.com',
            'status' => ReferralStatus::CONVERTED,
        ]);

        $commissionB = Commission::create([
            'partner_id' => $partnerBProfile->id,
            'referral_id' => $referralB->id,
            'base_amount' => 50000.00,
            'commission_rate' => 25.00,
            'commission_amount' => 12500.00,
            'status' => CommissionStatus::APPROVED,
        ]);

        // Partner A attempts to view Partner B's referral -> 403 Forbidden
        $responseRef = $this->actingAs($this->partnerUser)->get(route('partner.referrals.show', $referralB));
        $responseRef->assertStatus(403);

        // Partner A attempts to view Partner B's commission -> 403 Forbidden
        $responseComm = $this->actingAs($this->partnerUser)->get(route('partner.earnings.show', $commissionB));
        $responseComm->assertStatus(403);
    }

    public function test_commission_calculation_and_admin_workflow(): void
    {
        $commissionService = app(CommissionService::class);

        $referral = Referral::create([
            'partner_id' => $this->partnerProfile->id,
            'referral_code' => 'STZ-000001',
            'client_name' => 'Enterprise Buyer',
            'client_email' => 'buyer@enterprise.com',
            'status' => ReferralStatus::CONVERTED,
        ]);

        // Deal volume ₹100,000 at 20% commission
        $commission = $commissionService->createCommission(
            $referral,
            100000.00,
            null,
            null,
            null,
            'Verified payment on project delivery'
        );

        $this->assertEquals(20000.00, (float) $commission->commission_amount);
        $this->assertEquals(CommissionStatus::PENDING, $commission->status);

        // Admin approves commission
        $this->actingAs($this->admin)->patch(route('admin.partners.commissions.approve', $commission));
        $commission->refresh();
        $this->assertEquals(CommissionStatus::APPROVED, $commission->status);

        // Admin marks payable
        $this->actingAs($this->admin)->patch(route('admin.partners.commissions.payable', $commission));
        $commission->refresh();
        $this->assertEquals(CommissionStatus::PAYABLE, $commission->status);

        // Admin executes payout
        $payoutResponse = $this->actingAs($this->admin)->post(route('admin.partners.payouts.store'), [
            'partner_id' => $this->partnerProfile->id,
            'commission_ids' => [$commission->id],
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'UTR998877665544',
            'notes' => 'Batch payout Q4',
        ]);

        $payoutResponse->assertRedirect();

        $commission->refresh();
        $this->assertEquals(CommissionStatus::PAID, $commission->status);
        $this->assertNotNull($commission->payout_id);

        $payout = Payout::find($commission->payout_id);
        $this->assertNotNull($payout);
        $this->assertEquals(20000.00, (float) $payout->amount);
        $this->assertEquals(PayoutStatus::COMPLETED, $payout->status);
    }

    public function test_leaderboard_accuracy_and_period_filters(): void
    {
        $leaderboardService = app(LeaderboardService::class);

        // Create 2 referrals for Partner A
        $refA1 = Referral::create([
            'partner_id' => $this->partnerProfile->id,
            'referral_code' => 'STZ-000001',
            'client_name' => 'Client Alpha 1',
            'status' => ReferralStatus::CONVERTED,
        ]);
        Commission::create([
            'partner_id' => $this->partnerProfile->id,
            'referral_id' => $refA1->id,
            'base_amount' => 50000.00,
            'commission_rate' => 20.00,
            'commission_amount' => 10000.00,
            'status' => CommissionStatus::PAID,
        ]);

        // Create Partner B with higher volume
        $userB = User::create([
            'name' => 'High Earner',
            'email' => 'highearner@partnerdomain.com',
            'password' => Hash::make('Password@123'),
            'role' => UserRole::PARTNER,
            'status' => UserStatus::ACTIVE,
        ]);
        $partnerB = PartnerProfile::create([
            'user_id' => $userB->id,
            'referral_code' => 'STZ-000002',
            'commission_rate' => 20.00,
            'status' => PartnerStatus::APPROVED,
            'payout_method' => 'upi',
            'payout_details' => 'high@upi',
        ]);
        $refB1 = Referral::create([
            'partner_id' => $partnerB->id,
            'referral_code' => 'STZ-000002',
            'client_name' => 'Client Beta 1',
            'status' => ReferralStatus::CONVERTED,
        ]);
        Commission::create([
            'partner_id' => $partnerB->id,
            'referral_id' => $refB1->id,
            'base_amount' => 150000.00,
            'commission_rate' => 20.00,
            'commission_amount' => 30000.00,
            'status' => CommissionStatus::APPROVED,
        ]);

        $board = $leaderboardService->getLeaderboard('all');
        $this->assertNotEmpty($board);

        // Rank 1 should be Partner B ($30k earnings)
        $this->assertEquals($partnerB->id, $board[0]['partner_id']);
        $this->assertEquals(1, $board[0]['rank']);

        // Rank 2 should be Partner A ($10k earnings)
        $this->assertEquals($this->partnerProfile->id, $board[1]['partner_id']);
        $this->assertEquals(2, $board[1]['rank']);

        // Leaderboard page renders properly
        $response = $this->actingAs($this->partnerUser)->get('/partner/leaderboard?period=all');
        $response->assertStatus(200);
        $response->assertSee('High Earner');
        $response->assertSee('Top Partner');
    }

    public function test_admin_partner_management_endpoints(): void
    {
        // Admin index
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.partners.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($this->partnerProfile->referral_code);

        // Admin show
        $showResponse = $this->actingAs($this->admin)->get(route('admin.partners.show', $this->partnerProfile));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($this->partnerProfile->referral_code);

        // Admin update rate
        $rateResponse = $this->actingAs($this->admin)->patch(route('admin.partners.rate', $this->partnerProfile), [
            'commission_rate' => 27.50,
        ]);
        $rateResponse->assertRedirect();
        $this->partnerProfile->refresh();
        $this->assertEquals(27.50, (float) $this->partnerProfile->commission_rate);
    }
}
