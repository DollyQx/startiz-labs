<?php

namespace Tests\Feature\Client;

use App\Enums\ProjectStatus;
use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private User $clientA;
    private User $clientB;
    private User $admin;
    private Project $projectA;
    private Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientA = User::factory()->create([
            'name' => 'Client Alpha',
            'email' => 'client.alpha@ticket.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Client Beta',
            'email' => 'client.beta@ticket.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ticket.test',
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Project Alpha',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Project Beta',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);
    }

    public function test_client_can_view_own_tickets_directory_and_is_isolated_from_others(): void
    {
        $ticketA = SupportTicket::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'subject' => 'Alpha API Gateway Error',
            'description' => '500 Server Error on webhook trigger.',
            'category' => TicketCategory::TECHNICAL,
            'priority' => TicketPriority::HIGH,
            'status' => TicketStatus::OPEN,
        ]);

        $ticketB = SupportTicket::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'subject' => 'Beta Billing Inquiry',
            'description' => 'Questions about tax calculation.',
            'category' => TicketCategory::BILLING,
            'priority' => TicketPriority::MEDIUM,
            'status' => TicketStatus::OPEN,
        ]);

        // Client A index sees ticketA but not ticketB
        $this->actingAs($this->clientA)
            ->get(route('client.tickets.index'))
            ->assertStatus(200)
            ->assertSee($ticketA->reference_number)
            ->assertSee('Alpha API Gateway Error')
            ->assertDontSee($ticketB->reference_number)
            ->assertDontSee('Beta Billing Inquiry');
    }

    public function test_client_can_create_ticket_with_auto_generated_reference_number(): void
    {
        $response = $this->actingAs($this->clientA)
            ->post(route('client.tickets.store'), [
                'subject' => 'Database Sync Delay',
                'description' => 'Replica database lag is exceeding 5 seconds.',
                'category' => 'technical',
                'priority' => 'high',
                'project_id' => $this->projectA->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Verify ticket DB record
        $ticket = SupportTicket::where('subject', 'Database Sync Delay')->first();
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('STZ-TKT-', $ticket->reference_number);
        $this->assertEquals($this->clientA->id, $ticket->client_id);
        $this->assertEquals(TicketStatus::OPEN, $ticket->status);

        // Verify initial message created
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_id' => $this->clientA->id,
            'is_internal' => false,
            'message' => 'Replica database lag is exceeding 5 seconds.',
        ]);

        // Verify activity log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'ticket.created',
            'subject_type' => SupportTicket::class,
            'subject_id' => $ticket->id,
        ]);
    }

    public function test_client_project_ownership_validation_rejects_cross_client_project_ids(): void
    {
        // Client A attempts creating a ticket associated with Client B's project
        $response = $this->actingAs($this->clientA)
            ->post(route('client.tickets.store'), [
                'subject' => 'Malicious Project Assignment',
                'description' => 'Trying to access Client B project.',
                'category' => 'general',
                'priority' => 'medium',
                'project_id' => $this->projectB->id, // Client B project!
            ]);

        $response->assertSessionHasErrors(['project_id']);
        $this->assertDatabaseMissing('support_tickets', [
            'subject' => 'Malicious Project Assignment',
        ]);
    }

    public function test_client_can_view_own_ticket_workspace_and_reply(): void
    {
        $ticket = SupportTicket::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'subject' => 'SSL Certificate Renewal',
            'description' => 'SSL certificate expires in 3 days.',
            'category' => TicketCategory::TECHNICAL,
            'priority' => TicketPriority::HIGH,
            'status' => TicketStatus::WAITING_FOR_CLIENT,
        ]);

        // Client A show ticket workspace -> 200 OK
        $this->actingAs($this->clientA)
            ->get(route('client.tickets.show', $ticket->id))
            ->assertStatus(200)
            ->assertSee($ticket->reference_number)
            ->assertSee('SSL Certificate Renewal');

        // Client A replies
        $response = $this->actingAs($this->clientA)
            ->post(route('client.tickets.reply', $ticket->id), [
                'message' => 'Here is the DNS TXT record validation string.',
            ]);

        $response->assertRedirect();

        // Verify message posted as public
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_id' => $this->clientA->id,
            'is_internal' => false,
            'message' => 'Here is the DNS TXT record validation string.',
        ]);

        // Verify status updated to IN_PROGRESS
        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_client_cannot_view_or_reply_to_another_clients_ticket(): void
    {
        $ticketB = SupportTicket::create([
            'client_id' => $this->clientB->id,
            'subject' => 'Private Client B Ticket',
            'description' => 'Confidential details.',
            'category' => TicketCategory::ACCOUNT,
            'priority' => TicketPriority::MEDIUM,
            'status' => TicketStatus::OPEN,
        ]);

        // Client A view ticketB -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.tickets.show', $ticketB->id))
            ->assertStatus(403);

        // Client A reply to ticketB -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->post(route('client.tickets.reply', $ticketB->id), [
                'message' => 'Unauthorized injection attempt.',
            ])
            ->assertStatus(403);
    }

    public function test_internal_notes_are_strictly_hidden_from_client_views(): void
    {
        $ticket = SupportTicket::create([
            'client_id' => $this->clientA->id,
            'subject' => 'Server Crash Investigation',
            'description' => 'Nginx memory leak under load.',
            'category' => TicketCategory::TECHNICAL,
            'priority' => TicketPriority::URGENT,
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        // Admin adds public reply
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $this->admin->id,
            'message' => 'We are analyzing kernel memory logs now.',
            'is_internal' => false,
        ]);

        // Admin adds confidential internal note
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $this->admin->id,
            'message' => 'TOP SECRET INTERNAL NOTE: Client server disk is 99% full.',
            'is_internal' => true,
        ]);

        // Client views ticket workspace
        $response = $this->actingAs($this->clientA)
            ->get(route('client.tickets.show', $ticket->id))
            ->assertStatus(200);

        // Client SHOULD see public message
        $response->assertSee('We are analyzing kernel memory logs now.');

        // Client MUST NOT see internal note
        $response->assertDontSee('TOP SECRET INTERNAL NOTE');

        // Confirm database level clientVisibleMessages relation excludes internal note
        $this->assertEquals(1, $ticket->clientVisibleMessages()->count());
    }
}
