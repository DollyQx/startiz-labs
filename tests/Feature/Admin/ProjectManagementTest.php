<?php

namespace Tests\Feature\Admin;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\RequirementPriority;
use App\Enums\RequirementStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectRequirement;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->client = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);
    }

    public function test_admin_can_view_project_directory_list(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'E-Commerce Platform Core',
            'status' => ProjectStatus::PLANNING->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.projects.index'))
            ->assertStatus(200)
            ->assertSee('E-Commerce Platform Core')
            ->assertSee($project->reference_number);
    }

    public function test_admin_can_render_project_creation_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.projects.create'))
            ->assertStatus(200)
            ->assertSee('Create New Project');
    }

    public function test_admin_can_create_new_project_workspace(): void
    {
        $payload = [
            'client_id' => $this->client->id,
            'title' => 'Mobile App API Backend',
            'description' => 'RESTful API for iOS and Android clients.',
            'status' => 'planning',
            'priority' => 'high',
            'start_date' => now()->format('Y-m-d'),
            'expected_completion_date' => now()->addMonths(2)->format('Y-m-d'),
            'estimated_value' => 250000.00,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $payload);

        $this->assertDatabaseHas('projects', [
            'client_id' => $this->client->id,
            'title' => 'Mobile App API Backend',
            'status' => 'planning',
            'priority' => 'high',
            'estimated_value' => 250000.00,
        ]);

        $project = Project::where('title', 'Mobile App API Backend')->first();
        $this->assertNotNull($project->reference_number);
        $this->assertStringStartsWith('STZ-PROJ', $project->reference_number);

        $response->assertRedirect(route('admin.projects.show', $project->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'action' => 'project.created',
        ]);
    }

    public function test_project_creation_fails_with_invalid_validation_data(): void
    {
        $payload = [
            'client_id' => 99999, // non-existent client
            'title' => '',
            'status' => 'invalid_status',
            'priority' => 'invalid_priority',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $payload)
            ->assertSessionHasErrors(['client_id', 'title', 'status', 'priority']);
    }

    public function test_admin_can_view_project_workspace_detail(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'CRM Integration Portal',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.projects.show', $project->id))
            ->assertStatus(200)
            ->assertSee($project->reference_number)
            ->assertSee('CRM Integration Portal');
    }

    public function test_admin_can_update_project_information(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'Original Project Title',
            'status' => ProjectStatus::PLANNING->value,
        ]);

        $payload = [
            'client_id' => $this->client->id,
            'title' => 'Updated Project Title',
            'status' => 'in_progress',
            'priority' => 'urgent',
            'description' => 'Updated scope notes.',
            'estimated_value' => 180000.00,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project->id), $payload);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project Title',
            'status' => 'in_progress',
            'priority' => 'urgent',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'action' => 'project.updated',
        ]);
    }

    public function test_admin_can_transition_project_status(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'status' => ProjectStatus::PLANNING->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.projects.status', $project->id), [
                'status' => 'in_progress',
            ]);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'action' => 'project.status_updated',
        ]);
    }

    public function test_admin_can_create_requirement_for_project(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $payload = [
            'title' => 'Single Sign-On Authentication Support',
            'description' => 'Must support OAuth2 and Google Workspace SSO.',
            'status' => 'submitted',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.requirements.store', $project->id), $payload);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('project_requirements', [
            'project_id' => $project->id,
            'title' => 'Single Sign-On Authentication Support',
            'priority' => 'high',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => ProjectRequirement::class,
            'action' => 'requirement.created',
        ]);
    }

    public function test_admin_can_update_requirement_status(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $requirement = ProjectRequirement::create([
            'project_id' => $project->id,
            'submitted_by_id' => $this->client->id,
            'title' => 'Requirement Alpha',
            'status' => RequirementStatus::SUBMITTED->value,
            'priority' => RequirementPriority::MEDIUM->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.projects.requirements.status', [$project->id, $requirement->id]), [
                'status' => 'approved',
            ]);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('project_requirements', [
            'id' => $requirement->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_create_milestone_for_project(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $payload = [
            'title' => 'Milestone 1: Prototype Delivery',
            'description' => 'Delivering working UI wireframes and database schema.',
            'sequence_order' => 1,
            'status' => 'pending',
            'amount' => 50000.00,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.milestones.store', $project->id), $payload);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('project_milestones', [
            'project_id' => $project->id,
            'title' => 'Milestone 1: Prototype Delivery',
            'sequence_order' => 1,
            'amount' => 50000.00,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => ProjectMilestone::class,
            'action' => 'milestone.created',
        ]);
    }

    public function test_admin_can_update_milestone_status(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 1 MVP',
            'sequence_order' => 1,
            'status' => MilestoneStatus::IN_PROGRESS->value,
            'amount' => 25000.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.projects.milestones.status', [$project->id, $milestone->id]), [
                'status' => 'completed',
            ]);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('project_milestones', [
            'id' => $milestone->id,
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_create_task_linked_to_milestone(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Milestone 1',
            'sequence_order' => 1,
            'status' => MilestoneStatus::PENDING->value,
            'amount' => 10000.00,
        ]);

        $payload = [
            'title' => 'Setup User Middleware',
            'description' => 'Ensure RBAC check for admin routes.',
            'status' => 'todo',
            'priority' => 'high',
            'assigned_user_id' => $this->admin->id,
            'milestone_id' => $milestone->id,
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.tasks.store', $project->id), $payload);

        $response->assertRedirect(route('admin.projects.show', $project->id));

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'assigned_user_id' => $this->admin->id,
            'title' => 'Setup User Middleware',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Task::class,
            'action' => 'task.created',
        ]);
    }

    public function test_task_creation_fails_if_milestone_belongs_to_another_project(): void
    {
        $projectA = Project::factory()->create(['client_id' => $this->client->id]);
        $projectB = Project::factory()->create(['client_id' => $this->client->id]);

        $milestoneA = ProjectMilestone::create([
            'project_id' => $projectA->id,
            'title' => 'Project A Milestone',
            'sequence_order' => 1,
            'status' => MilestoneStatus::PENDING->value,
            'amount' => 10000.00,
        ]);

        $payload = [
            'title' => 'Cross-project task attempt',
            'status' => 'todo',
            'priority' => 'medium',
            'milestone_id' => $milestoneA->id, // milestone belongs to Project A, but post to Project B
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.projects.tasks.store', $projectB->id), $payload)
            ->assertSessionHasErrors(['milestone_id']);
    }

    public function test_non_admin_users_cannot_access_project_management(): void
    {
        // Unauthenticated user
        $this->get(route('admin.projects.index'))
            ->assertRedirect(route('login'));

        // Client user
        $this->actingAs($this->client)
            ->get(route('admin.projects.index'))
            ->assertStatus(403);
    }
}
