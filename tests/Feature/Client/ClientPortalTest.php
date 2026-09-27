<?php

namespace Tests\Feature\Client;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Enums\RequirementStatus;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ClientProfile;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectRequirement;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $clientA;
    private User $clientB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientA = User::factory()->create([
            'name' => 'Client Alpha',
            'email' => 'client.alpha@example.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Client Beta',
            'email' => 'client.beta@example.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);
    }

    public function test_guest_cannot_access_client_dashboard(): void
    {
        $this->get(route('client.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_client_can_access_dashboard_and_see_own_metrics(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha Web Application',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.dashboard'))
            ->assertStatus(200)
            ->assertSee('Welcome back, Client Alpha')
            ->assertSee('Alpha Web Application')
            ->assertSee('Project Overview');
    }

    public function test_client_dashboard_only_contains_authenticated_clients_data(): void
    {
        $projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha Private Project',
        ]);

        $projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Beta Secret System',
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.dashboard'))
            ->assertStatus(200)
            ->assertSee('Alpha Private Project')
            ->assertDontSee('Beta Secret System');
    }

    public function test_client_can_view_own_project_directory_and_filter(): void
    {
        $project1 = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'E-Commerce Portal',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $project2 = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Mobile App iOS',
            'status' => ProjectStatus::COMPLETED,
        ]);

        // Access project index
        $this->actingAs($this->clientA)
            ->get(route('client.projects.index'))
            ->assertStatus(200)
            ->assertSee('E-Commerce Portal')
            ->assertSee('Mobile App iOS');

        // Filter by search
        $this->actingAs($this->clientA)
            ->get(route('client.projects.index', ['search' => 'E-Commerce']))
            ->assertStatus(200)
            ->assertSee('E-Commerce Portal')
            ->assertDontSee('Mobile App iOS');

        // Filter by status
        $this->actingAs($this->clientA)
            ->get(route('client.projects.index', ['status' => 'completed']))
            ->assertStatus(200)
            ->assertSee('Mobile App iOS')
            ->assertDontSee('E-Commerce Portal');
    }

    public function test_client_can_view_own_project_workspace(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'CRM Cloud Platform',
            'description' => 'Custom enterprise CRM portal development.',
            'notes' => 'INTERNAL SECRET ADMIN NOTE DO NOT EXPOSE',
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 1 MVP Milestone',
            'status' => MilestoneStatus::IN_PROGRESS,
            'sequence_order' => 1,
        ]);

        $requirement = ProjectRequirement::create([
            'project_id' => $project->id,
            'title' => 'OAuth2 Authentication Requirement',
            'status' => RequirementStatus::APPROVED,
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Configure SSL Security Task',
            'status' => TaskStatus::COMPLETED,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.projects.show', $project->id))
            ->assertStatus(200)
            ->assertSee('CRM Cloud Platform')
            ->assertSee('Phase 1 MVP Milestone')
            ->assertSee('OAuth2 Authentication Requirement')
            ->assertSee('Configure SSL Security Task')
            ->assertDontSee('INTERNAL SECRET ADMIN NOTE DO NOT EXPOSE');
    }

    public function test_strict_cross_client_isolation_enforced(): void
    {
        $projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Client A Confidential Project',
        ]);

        $projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Client B Confidential Project',
        ]);

        // Client A accesses Project A -> 200 OK
        $this->actingAs($this->clientA)
            ->get(route('client.projects.show', $projectA->id))
            ->assertStatus(200);

        // Client A attempts accessing Project B -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.projects.show', $projectB->id))
            ->assertStatus(403);

        // Client B attempts accessing Project A -> 403 Forbidden
        $this->actingAs($this->clientB)
            ->get(route('client.projects.show', $projectA->id))
            ->assertStatus(403);
    }

    public function test_client_can_view_and_update_allowed_profile_fields(): void
    {
        $profile = ClientProfile::factory()->create([
            'user_id' => $this->clientA->id,
            'company_name' => 'Initial Company',
        ]);

        // View profile
        $this->actingAs($this->clientA)
            ->get(route('client.profile'))
            ->assertStatus(200)
            ->assertSee('Client Account Profile')
            ->assertSee('client.alpha@example.test');

        // Update permitted fields
        $this->actingAs($this->clientA)
            ->put(route('client.profile.update'), [
                'name' => 'Client Alpha Updated',
                'phone' => '+15551234567',
                'company_name' => 'Updated Tech Corp',
                'city' => 'San Francisco',
                'country' => 'USA',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $this->clientA->id,
            'name' => 'Client Alpha Updated',
            'role' => UserRole::CLIENT->value, // Role untouched
        ]);

        $this->assertDatabaseHas('client_profiles', [
            'user_id' => $this->clientA->id,
            'company_name' => 'Updated Tech Corp',
            'city' => 'San Francisco',
        ]);
    }
}
