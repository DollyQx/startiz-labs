<?php

namespace Tests\Feature\Admin;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $supportStaff;
    private User $client;
    private SupportTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@support.test',
            'role' => UserRole::SUPER_ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->supportStaff = User::factory()->create([
            'name' => 'Support Tech Agent',
            'email' => 'tech@support.test',
            'role' => UserRole::SUPPORT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->client = User::factory()->create([
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->ticket = SupportTicket::create([
            'client_id' => $this->client->id,
            'subject' => 'Production Database Connectivity Timeout',
            'description' => 'Connections to PostgreSQL pool timing out under peak load.',
            'category' => TicketCategory::TECHNICAL,
            'priority' => TicketPriority::HIGH,
            'status' => TicketStatus::OPEN,
        ]);
    }

    public function test_admin_can_view_support_tickets_directory_with_filtering(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.tickets.index'))
            ->assertStatus(200)
            ->assertSee($this->ticket->reference_number)
            ->assertSee('Production Database Connectivity Timeout')
            ->assertSee('Acme Corporation');

        // Test filtering by status
        $this->actingAs($this->admin)
            ->get(route('admin.tickets.index', ['status' => 'open']))
            ->assertStatus(200)
            ->assertSee($this->ticket->reference_number);

        $this->actingAs($this->admin)
            ->get(route('admin.tickets.index', ['status' => 'closed']))
            ->assertStatus(200)
            ->assertDontSee($this->ticket->reference_number);
    }

    public function test_admin_can_view_ticket_workspace_including_internal_notes(): void
    {
        // Public message
        SupportTicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'sender_id' => $this->client->id,
            'message' => 'Please update us on the issue.',
            'is_internal' => false,
        ]);

        // Internal note
        SupportTicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'sender_id' => $this->supportStaff->id,
            'message' => 'INTERNAL DIAGNOSTIC: PgBouncer pool size needs scaling to 100.',
            'is_internal' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tickets.show', $this->ticket->id))
            ->assertStatus(200);

        // Admin MUST see both public message and internal note
        $response->assertSee('Please update us on the issue.');
        $response->assertSee('INTERNAL DIAGNOSTIC: PgBouncer pool size needs scaling to 100.');
        $response->assertSee('Internal Staff Note');
    }

    public function test_admin_can_reply_publicly_to_client(): void
    {
        $response = $this->actingAs($this->supportStaff)
            ->post(route('admin.tickets.reply', $this->ticket->id), [
                'message' => 'We have adjusted PgBouncer settings and restarted the cluster.',
                'status' => 'waiting_for_client',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $this->ticket->id,
            'sender_id' => $this->supportStaff->id,
            'is_internal' => false,
            'message' => 'We have adjusted PgBouncer settings and restarted the cluster.',
        ]);

        $this->assertEquals(TicketStatus::WAITING_FOR_CLIENT, $this->ticket->fresh()->status);
    }

    public function test_admin_can_add_internal_note(): void
    {
        $response = $this->actingAs($this->supportStaff)
            ->post(route('admin.tickets.internal-note', $this->ticket->id), [
                'message' => 'CONFIDENTIAL: Customer is on Enterprise tier, escalate if downtime > 15 mins.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $this->ticket->id,
            'sender_id' => $this->supportStaff->id,
            'is_internal' => true,
            'message' => 'CONFIDENTIAL: Customer is on Enterprise tier, escalate if downtime > 15 mins.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'ticket.internal_note_added',
            'subject_id' => $this->ticket->id,
        ]);
    }

    public function test_admin_can_update_status_and_timestamps(): void
    {
        // Resolve ticket
        $this->actingAs($this->admin)
            ->patch(route('admin.tickets.status', $this->ticket->id), [
                'status' => 'resolved',
            ])
            ->assertRedirect();

        $this->assertEquals(TicketStatus::RESOLVED, $this->ticket->fresh()->status);
        $this->assertNotNull($this->ticket->fresh()->resolved_at);

        // Close ticket
        $this->actingAs($this->admin)
            ->patch(route('admin.tickets.status', $this->ticket->id), [
                'status' => 'closed',
            ])
            ->assertRedirect();

        $this->assertEquals(TicketStatus::CLOSED, $this->ticket->fresh()->status);
        $this->assertNotNull($this->ticket->fresh()->closed_at);
    }

    public function test_admin_can_reassign_ticket_staff(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.tickets.assign', $this->ticket->id), [
                'assigned_to_id' => $this->supportStaff->id,
            ])
            ->assertRedirect();

        $this->assertEquals($this->supportStaff->id, $this->ticket->fresh()->assigned_to_id);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'ticket.assigned',
            'subject_id' => $this->ticket->id,
        ]);
    }
}
