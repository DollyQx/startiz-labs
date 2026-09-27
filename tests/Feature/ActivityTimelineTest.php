<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Enums\ProjectStatus;
use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $clientA;
    protected User $clientB;
    protected Project $projectA;
    protected Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientA = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Project Alpha',
            'status' => ProjectStatus::IN_PROGRESS->value,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Project Beta',
            'status' => ProjectStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_authorized_admin_can_view_activity_audit_directory()
    {
        ActivityLogger::log('project.created', $this->projectA, 'Created Project Alpha');

        $response = $this->actingAs($this->admin)->get(route('admin.activity.index'));

        $response->assertOk();
        $response->assertViewIs('admin.activity.index');
        $response->assertSee('System Audit');
        $response->assertSee('Activity Logs');
        $response->assertSee('Created Project Alpha');
    }

    public function test_unauthorized_client_cannot_access_admin_activity_directory()
    {
        $response = $this->actingAs($this->clientA)->get(route('admin.activity.index'));

        $response->assertStatus(403);
    }

    public function test_admin_activity_filtering_by_action_client_and_project_works()
    {
        ActivityLogger::log('project.created', $this->projectA, 'Project Alpha Created');
        ActivityLogger::log('quotation.sent', $this->projectB, 'Quotation Sent for Beta');

        // Filter by Project A
        $response = $this->actingAs($this->admin)->get(route('admin.activity.index', [
            'project_id' => $this->projectA->id,
        ]));

        $response->assertOk();
        $response->assertSee('Project Alpha Created');
        $response->assertDontSee('Quotation Sent for Beta');
    }

    public function test_admin_can_view_audit_log_details_with_sanitized_metadata()
    {
        $log = ActivityLogger::log(
            action: 'payment.recorded',
            subject: $this->projectA,
            description: 'Payment recorded via Gateway',
            metadata: [
                'amount' => 5000,
                'secret_token' => 'super_secret_api_key_12345',
                'gateway' => 'Razorpay',
            ]
        );

        $response = $this->actingAs($this->admin)->get(route('admin.activity.show', $log->id));

        $response->assertOk();
        $response->assertSee('Event Payload Metadata (Sanitized)');
        $response->assertSee('******** [REDACTED]');
        $response->assertDontSee('super_secret_api_key_12345');
    }

    public function test_client_can_view_their_own_project_activity_timeline()
    {
        ActivityLogger::log('project.created', $this->projectA, 'Project Alpha initialized');

        $response = $this->actingAs($this->clientA)->get(route('client.projects.activity', $this->projectA->id));

        $response->assertOk();
        $response->assertSee('Project Alpha initialized');
    }

    public function test_client_cannot_view_another_clients_project_activity_timeline()
    {
        ActivityLogger::log('project.created', $this->projectB, 'Project Beta initialized');

        $response = $this->actingAs($this->clientA)->get(route('client.projects.activity', $this->projectB->id));

        $response->assertStatus(403);
    }

    public function test_internal_staff_notes_are_hidden_from_client_activity_timeline()
    {
        $ticket = SupportTicket::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'subject' => 'Issue with login',
            'description' => 'Unable to login to staging environment',
            'category' => TicketCategory::TECHNICAL,
            'priority' => TicketPriority::HIGH,
            'status' => TicketStatus::OPEN,
        ]);

        ActivityLogger::log('ticket.created', $ticket, 'Support ticket created');
        ActivityLogger::log('ticket.internal_note_added', $ticket, 'Internal Note: Client might be misconfiguring DNS');

        $response = $this->actingAs($this->clientA)->get(route('client.projects.activity', $this->projectA->id));

        $response->assertOk();
        $response->assertSee('Support ticket created');
        $response->assertDontSee('Internal Note: Client might be misconfiguring DNS');
    }

    public function test_private_documents_are_hidden_from_client_activity_timeline()
    {
        $publicDoc = Document::create([
            'project_id' => $this->projectA->id,
            'client_id' => $this->clientA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::OTHER->value,
            'original_filename' => 'spec.pdf',
            'storage_path' => 'documents/spec.pdf',
            'visibility' => DocumentVisibility::CLIENT->value,
            'description' => 'Client Shared Spec Sheet',
        ]);

        $privateDoc = Document::create([
            'project_id' => $this->projectA->id,
            'client_id' => $this->clientA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::OTHER->value,
            'original_filename' => 'internal.pdf',
            'storage_path' => 'documents/internal.pdf',
            'visibility' => DocumentVisibility::PRIVATE->value,
            'description' => 'Internal Architecture Review',
        ]);

        ActivityLogger::log('document_uploaded', $publicDoc, 'Uploaded public spec sheet');
        ActivityLogger::log('document_uploaded', $privateDoc, 'Uploaded internal architecture review');

        $response = $this->actingAs($this->clientA)->get(route('client.projects.activity', $this->projectA->id));

        $response->assertOk();
        $response->assertSee('Uploaded public spec sheet');
        $response->assertDontSee('Uploaded internal architecture review');
    }

    public function test_activity_logger_automatically_resolves_project_and_client_id()
    {
        $log = ActivityLogger::log('project.status_updated', $this->projectA, 'Updated status to IN_PROGRESS');

        $this->assertEquals($this->projectA->id, $log->project_id);
        $this->assertEquals($this->clientA->id, $log->client_id);
    }

    public function test_client_all_projects_activity_timeline_shows_only_client_owned_events()
    {
        ActivityLogger::log('project.created', $this->projectA, 'Client A Event');
        ActivityLogger::log('project.created', $this->projectB, 'Client B Event');

        $response = $this->actingAs($this->clientA)->get(route('client.activity.index'));

        $response->assertOk();
        $response->assertSee('Client A Event');
        $response->assertDontSee('Client B Event');
    }
}
