<?php

namespace Tests\Feature;

use App\Enums\ChangeRequestPriority;
use App\Enums\ChangeRequestStatus;
use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Enums\InvoiceStatus;
use App\Enums\QuotationStatus;
use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\ChangeRequest;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\GenericDatabaseNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $clientA;
    protected User $clientB;
    protected Project $projectA;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $this->clientA = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => 'active',
        ]);

        $this->clientB = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => 'active',
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Client A Portal Project',
        ]);
    }

    /** @test */
    public function notification_service_dispatches_database_notifications_correctly()
    {
        NotificationService::notifyUser(
            $this->clientA,
            'system',
            'Test Title',
            'Test notification message content.',
            '/client/dashboard'
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
            'notifiable_type' => User::class,
            'type' => GenericDatabaseNotification::class,
        ]);

        $notification = $this->clientA->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('system', $notification->data['category']);
        $this->assertEquals('Test Title', $notification->data['title']);
        $this->assertEquals('Test notification message content.', $notification->data['message']);
        $this->assertEquals('/client/dashboard', $notification->data['url']);
        $this->assertNull($notification->read_at);
    }

    /** @test */
    public function recipient_isolation_prevents_client_from_accessing_another_clients_notifications()
    {
        NotificationService::notifyUser(
            $this->clientA,
            'invoice',
            'Client A Invoice Notification',
            'Invoice 1001 issued',
            '/client/invoices/1'
        );

        $notificationA = $this->clientA->notifications()->first();

        // Client B tries to mark Client A's notification as read
        $response = $this->actingAs($this->clientB)
            ->post(route('client.notifications.mark-read', $notificationA->id));

        $response->assertStatus(404);
        $this->assertNull($notificationA->fresh()->read_at);
    }

    /** @test */
    public function user_can_mark_notification_as_read_and_unread()
    {
        NotificationService::notifyUser(
            $this->clientA,
            'quotation',
            'Quotation Ready',
            'Your quotation is available.',
            '/client/quotations/1'
        );

        $notification = $this->clientA->notifications()->first();

        // Mark read
        $this->actingAs($this->clientA)
            ->post(route('client.notifications.mark-read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);

        // Mark unread
        $this->actingAs($this->clientA)
            ->post(route('client.notifications.mark-unread', $notification->id))
            ->assertRedirect();

        $this->assertNull($notification->fresh()->read_at);
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        NotificationService::notifyUser($this->clientA, 'system', 'Note 1', 'Message 1');
        NotificationService::notifyUser($this->clientA, 'system', 'Note 2', 'Message 2');

        $this->assertEquals(2, $this->clientA->unreadNotifications()->count());

        $this->actingAs($this->clientA)
            ->post(route('client.notifications.mark-all-read'))
            ->assertRedirect();

        $this->assertEquals(0, $this->clientA->unreadNotifications()->count());
    }

    /** @test */
    public function quotation_status_transition_to_sent_triggers_client_notification()
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->clientA->id,
            'status' => QuotationStatus::DRAFT,
            'total' => 15000.00,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.quotations.status', $quotation->id), [
                'status' => QuotationStatus::SENT->value,
            ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);

        $notification = $this->clientA->notifications()->first();
        $this->assertEquals('quotation', $notification->data['category']);
        $this->assertStringContainsString('New Quotation Issued', $notification->data['title']);
    }

    /** @test */
    public function client_accepting_quotation_notifies_client_and_admin_staff()
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->clientA->id,
            'status' => QuotationStatus::SENT,
            'total' => 20000.00,
        ]);

        $this->actingAs($this->clientA)
            ->post(route('client.quotations.accept', $quotation->id))
            ->assertRedirect();

        // Client gets confirmation notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);

        // Admin staff gets notified
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function invoice_issuance_notifies_client()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), [
                'client_id' => $this->clientA->id,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'subtotal' => 5000.00,
                'tax' => 900.00,
                'discount' => 0.00,
                'status' => InvoiceStatus::ISSUED->value,
            ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);

        $notification = $this->clientA->notifications()->first();
        $this->assertEquals('invoice', $notification->data['category']);
        $this->assertStringContainsString('New Invoice Issued', $notification->data['title']);
    }

    /** @test */
    public function recording_payment_notifies_client_and_admin()
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->clientA->id,
            'total' => 10000.00,
            'amount_due' => 10000.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.payments.store', $invoice->id), [
                'amount' => 5000.00,
                'payment_method' => 'bank_transfer',
                'paid_at' => now()->toDateString(),
            ])->assertRedirect();

        // Client notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);

        // Admin notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function support_ticket_creation_and_replies_trigger_notifications()
    {
        // Client submits support ticket
        $this->actingAs($this->clientA)
            ->post(route('client.tickets.store'), [
                'subject' => 'Cannot access project portal',
                'description' => 'Login error on project dashboard page.',
                'category' => TicketCategory::TECHNICAL->value,
                'priority' => TicketPriority::HIGH->value,
            ])->assertRedirect();

        $ticket = SupportTicket::where('client_id', $this->clientA->id)->first();
        $this->assertNotNull($ticket);

        // Staff (Admin) gets notified of new ticket
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
        ]);

        // Staff replies publicly -> Client gets notified
        $this->actingAs($this->admin)
            ->post(route('admin.tickets.reply', $ticket->id), [
                'message' => 'We are reviewing your access permissions now.',
            ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);

        // Staff posts internal note -> Client MUST NOT be notified
        $initialClientNotificationCount = $this->clientA->notifications()->count();

        $this->actingAs($this->admin)
            ->post(route('admin.tickets.internal-note', $ticket->id), [
                'message' => 'Internal note: check server logs for 500 error.',
            ])->assertRedirect();

        $this->assertEquals($initialClientNotificationCount, $this->clientA->notifications()->count());
    }

    /** @test */
    public function change_request_workflow_triggers_notifications()
    {
        // Client submits change request
        $this->actingAs($this->clientA)
            ->post(route('client.change-requests.store'), [
                'project_id' => $this->projectA->id,
                'title' => 'Add Dark Mode Toggle',
                'description' => 'Requesting dark theme implementation.',
                'priority' => ChangeRequestPriority::MEDIUM->value,
            ])->assertRedirect();

        $changeRequest = ChangeRequest::where('client_id', $this->clientA->id)->first();
        $this->assertNotNull($changeRequest);

        // Admin gets notified of new change request
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
        ]);

        // Admin approves change request -> Client gets notified
        $this->actingAs($this->admin)
            ->patch(route('admin.change-requests.approve', $changeRequest->id))
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);
    }

    /** @test */
    public function document_upload_with_client_visibility_notifies_client()
    {
        $file = UploadedFile::fake()->create('project_proposal.pdf', 500, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientA->id,
                'project_id' => $this->projectA->id,
                'document_type' => DocumentType::PROPOSAL->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'description' => 'Project Proposal v1.0',
                'file' => $file,
            ])->assertRedirect();

        // Client gets notified
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);
    }

    /** @test */
    public function document_upload_with_internal_visibility_does_not_notify_client()
    {
        $file = UploadedFile::fake()->create('internal_audit.pdf', 500, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientA->id,
                'project_id' => $this->projectA->id,
                'document_type' => DocumentType::OTHER->value,
                'visibility' => DocumentVisibility::PRIVATE->value,
                'description' => 'Internal Audit Report',
                'file' => $file,
            ])->assertRedirect();

        // Client MUST NOT get notified
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $this->clientA->id,
        ]);
    }
}
