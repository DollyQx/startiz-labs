<?php

namespace Tests\Feature\Admin;

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

class ChangeRequestManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Project $project;
    private ChangeRequest $changeRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Project Admin',
            'email' => 'admin@cr.test',
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->client = User::factory()->create([
            'name' => 'Acme Logistics',
            'email' => 'acme@cr.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'Fleet Tracking Platform',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->changeRequest = ChangeRequest::create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'title' => 'GPS Geofencing Alerts Integration',
            'description' => 'Send SMS notification when driver exits designated geofence polygon.',
            'priority' => ChangeRequestPriority::HIGH,
            'status' => ChangeRequestStatus::PENDING,
        ]);
    }

    public function test_admin_can_view_change_requests_directory_with_filtering(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.change-requests.index'))
            ->assertStatus(200)
            ->assertSee($this->changeRequest->reference_number)
            ->assertSee('GPS Geofencing Alerts Integration')
            ->assertSee('Acme Logistics');

        // Search filter
        $this->actingAs($this->admin)
            ->get(route('admin.change-requests.index', ['search' => 'Geofencing']))
            ->assertStatus(200)
            ->assertSee($this->changeRequest->reference_number);

        // Status filter
        $this->actingAs($this->admin)
            ->get(route('admin.change-requests.index', ['status' => 'approved']))
            ->assertStatus(200)
            ->assertDontSee($this->changeRequest->reference_number);
    }

    public function test_admin_can_view_change_request_workspace_including_internal_notes(): void
    {
        $this->changeRequest->update([
            'admin_notes' => 'INTERNAL STAFF OBSERVATION: Twilio SMS API costs apply.',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.change-requests.show', $this->changeRequest->id))
            ->assertStatus(200);

        $response->assertSee('GPS Geofencing Alerts Integration');
        $response->assertSee('INTERNAL STAFF OBSERVATION: Twilio SMS API costs apply.');
        $response->assertSee('Internal Staff Notes');
    }

    public function test_admin_can_update_scope_cost_time_assessment_and_notes(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.change-requests.review', $this->changeRequest->id), [
                'estimated_cost' => 28500.00,
                'estimated_days' => 4,
                'scope_impact' => 'Integration with Twilio SMS Gateway and Turf.js spatial calculation engine.',
                'client_notes' => 'Estimate includes 5000 SMS credits.',
                'admin_notes' => 'CONFIDENTIAL: Dev team capacity available starting next Monday.',
            ]);

        $response->assertRedirect();

        $cr = $this->changeRequest->fresh();
        $this->assertEquals(28500.00, $cr->estimated_cost);
        $this->assertEquals(4, $cr->estimated_days);
        $this->assertEquals(ChangeRequestStatus::UNDER_REVIEW, $cr->status);
        $this->assertEquals($this->admin->id, $cr->reviewed_by_id);
        $this->assertNotNull($cr->reviewed_at);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'change_request.reviewed',
            'subject_id' => $cr->id,
        ]);
    }

    public function test_admin_can_approve_change_request(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.change-requests.approve', $this->changeRequest->id));

        $response->assertRedirect();

        $cr = $this->changeRequest->fresh();
        $this->assertEquals(ChangeRequestStatus::APPROVED, $cr->status);
        $this->assertNotNull($cr->approved_at);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'change_request.approved',
            'subject_id' => $cr->id,
        ]);
    }

    public function test_admin_can_reject_change_request(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.change-requests.reject', $this->changeRequest->id));

        $response->assertRedirect();

        $cr = $this->changeRequest->fresh();
        $this->assertEquals(ChangeRequestStatus::REJECTED, $cr->status);
        $this->assertNotNull($cr->rejected_at);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'change_request.rejected',
            'subject_id' => $cr->id,
        ]);
    }

    public function test_non_negative_validation_on_cost_and_days_estimation(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.change-requests.review', $this->changeRequest->id), [
                'estimated_cost' => -150.00,
                'estimated_days' => -3,
            ]);

        $response->assertSessionHasErrors(['estimated_cost', 'estimated_days']);
    }
}
