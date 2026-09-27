<?php

namespace Tests\Feature\Client;

use App\Enums\ChangeRequestPriority;
use App\Enums\ChangeRequestStatus;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $clientA;
    private User $clientB;
    private Project $projectA;
    private Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientA = User::factory()->create([
            'name' => 'Client Alpha',
            'email' => 'client.alpha@cr.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Client Beta',
            'email' => 'client.beta@cr.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha CRM Upgrade',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Beta Portal Portal',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);
    }

    public function test_client_can_view_own_change_requests_directory_and_is_isolated(): void
    {
        $crA = ChangeRequest::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'title' => 'Alpha Custom Export Module',
            'description' => 'Add CSV and PDF export capability.',
            'priority' => ChangeRequestPriority::HIGH,
            'status' => ChangeRequestStatus::PENDING,
        ]);

        $crB = ChangeRequest::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'title' => 'Beta Multi-tenant Auth',
            'description' => 'Add OAuth2 SSO provider.',
            'priority' => ChangeRequestPriority::URGENT,
            'status' => ChangeRequestStatus::PENDING,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.change-requests.index'))
            ->assertStatus(200)
            ->assertSee($crA->reference_number)
            ->assertSee('Alpha Custom Export Module')
            ->assertDontSee($crB->reference_number)
            ->assertDontSee('Beta Multi-tenant Auth');
    }

    public function test_client_can_create_change_request_with_auto_generated_reference_number(): void
    {
        $response = $this->actingAs($this->clientA)
            ->post(route('client.change-requests.store'), [
                'project_id' => $this->projectA->id,
                'title' => 'Dark Mode & Custom Theme Switcher',
                'description' => 'Allow users to switch between dark and light themes dynamically.',
                'reason' => 'Required for accessibility compliance.',
                'priority' => 'medium',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $cr = ChangeRequest::where('title', 'Dark Mode & Custom Theme Switcher')->first();
        $this->assertNotNull($cr);
        $this->assertStringStartsWith('STZ-CR-', $cr->reference_number);
        $this->assertEquals($this->clientA->id, $cr->client_id);
        $this->assertEquals($this->projectA->id, $cr->project_id);
        $this->assertEquals(ChangeRequestStatus::PENDING, $cr->status);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'change_request.created',
            'subject_type' => ChangeRequest::class,
            'subject_id' => $cr->id,
        ]);
    }

    public function test_client_project_ownership_validation_rejects_cross_client_project_ids(): void
    {
        // Client A attempts to submit a change request for Client B's project
        $response = $this->actingAs($this->clientA)
            ->post(route('client.change-requests.store'), [
                'project_id' => $this->projectB->id, // Client B project!
                'title' => 'Malicious Scope Modification',
                'description' => 'Trying to modify unauthorized project.',
                'priority' => 'high',
            ]);

        $response->assertSessionHasErrors(['project_id']);
        $this->assertDatabaseMissing('change_requests', [
            'title' => 'Malicious Scope Modification',
        ]);
    }

    public function test_client_cannot_view_or_cancel_another_clients_change_request(): void
    {
        $crB = ChangeRequest::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'title' => 'Private Client B Request',
            'description' => 'Confidential request details.',
            'priority' => ChangeRequestPriority::MEDIUM,
            'status' => ChangeRequestStatus::PENDING,
        ]);

        // Client A view -> 403
        $this->actingAs($this->clientA)
            ->get(route('client.change-requests.show', $crB->id))
            ->assertStatus(403);

        // Client A cancel -> 403
        $this->actingAs($this->clientA)
            ->post(route('client.change-requests.cancel', $crB->id))
            ->assertStatus(403);
    }

    public function test_client_can_cancel_eligible_pending_request(): void
    {
        $cr = ChangeRequest::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'title' => 'Mistakenly Submitted Request',
            'description' => 'Created in error.',
            'priority' => ChangeRequestPriority::LOW,
            'status' => ChangeRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($this->clientA)
            ->post(route('client.change-requests.cancel', $cr->id));

        $response->assertRedirect();
        $this->assertEquals(ChangeRequestStatus::CANCELLED, $cr->fresh()->status);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'change_request.cancelled',
            'subject_id' => $cr->id,
        ]);
    }

    public function test_client_cannot_cancel_already_approved_request(): void
    {
        $cr = ChangeRequest::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'title' => 'Approved Feature Upgrade',
            'description' => 'Feature already approved by engineering.',
            'priority' => ChangeRequestPriority::HIGH,
            'status' => ChangeRequestStatus::APPROVED,
        ]);

        $response = $this->actingAs($this->clientA)
            ->post(route('client.change-requests.cancel', $cr->id));

        $response->assertStatus(403);
        $this->assertEquals(ChangeRequestStatus::APPROVED, $cr->fresh()->status);
    }

    public function test_internal_admin_notes_are_strictly_hidden_from_client(): void
    {
        $cr = ChangeRequest::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'title' => 'API Rate Limit Adjustment',
            'description' => 'Increase rate limit to 5000 req/min.',
            'priority' => ChangeRequestPriority::MEDIUM,
            'status' => ChangeRequestStatus::UNDER_REVIEW,
            'scope_impact' => 'Requires Redis cluster expansion.',
            'estimated_cost' => 15000.00,
            'estimated_days' => 3,
            'client_notes' => 'Commercial proposal valid for 30 days.',
            'admin_notes' => 'CONFIDENTIAL INTERNAL NOTE: High risk of Redis OOM if client exceeds memory limits.',
        ]);

        $response = $this->actingAs($this->clientA)
            ->get(route('client.change-requests.show', $cr->id))
            ->assertStatus(200);

        // Visible items
        $response->assertSee('API Rate Limit Adjustment');
        $response->assertSee('Requires Redis cluster expansion');
        $response->assertSee('15,000.00');
        $response->assertSee('Commercial proposal valid for 30 days');

        // MUST NOT see internal admin note
        $response->assertDontSee('CONFIDENTIAL INTERNAL NOTE');
        $response->assertDontSee('High risk of Redis OOM');
    }
}
