<?php

namespace Tests\Feature;

use App\Enums\DocumentVisibility;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectSignOffStatus;
use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectSignOff;
use App\Models\Quotation;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\GenericDatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndSystemIntegrationTest extends TestCase
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

        Storage::fake('local');
        Notification::fake();

        $this->admin = User::factory()->create([
            'name' => 'System Admin',
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->clientA = User::factory()->create([
            'name' => 'Client Alpha',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Client Beta',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha Core Platform',
            'status' => ProjectStatus::PLANNING,
            'estimated_value' => 50000.00,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Beta Mobile Application',
            'status' => ProjectStatus::IN_PROGRESS,
            'estimated_value' => 30000.00,
        ]);
    }

    /**
     * Complete end-to-end client journey verification.
     */
    public function test_complete_end_to_end_client_journey_from_quotation_to_project_completion()
    {
        // 1. Staff creates & sends Quotation to Client A
        $quotation = Quotation::create([
            'reference_number' => 'STZ-QT-E2E-01',
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now(),
            'created_by_id' => $this->admin->id,
            'title' => 'Alpha Core Development Proposal',
            'status' => QuotationStatus::SENT,
            'subtotal' => 50000.00,
            'tax' => 9000.00,
            'discount' => 0.00,
            'total' => 59000.00,
            'valid_until' => now()->addDays(14),
        ]);

        // 2. Client A logs in and accepts Quotation
        $acceptQuotationRes = $this->actingAs($this->clientA)
            ->post(route('client.quotations.accept', $quotation->id));
        $acceptQuotationRes->assertRedirect();
        $this->assertEquals(QuotationStatus::ACCEPTED, $quotation->fresh()->status);

        // 3. Staff creates Invoice for accepted quotation
        $invoice = Invoice::create([
            'reference_number' => 'STZ-INV-E2E-01',
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'quotation_id' => $quotation->id,
            'status' => InvoiceStatus::ISSUED,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 50000.00,
            'tax' => 9000.00,
            'discount' => 0.00,
            'total' => 59000.00,
            'amount_paid' => 0.00,
            'amount_due' => 59000.00,
        ]);

        // 4. Client A views invoice
        $viewInvoiceRes = $this->actingAs($this->clientA)
            ->get(route('client.invoices.show', $invoice->id));
        $viewInvoiceRes->assertOk();

        // 5. Payment recorded & verified
        $payment = Payment::create([
            'reference_number' => 'STZ-PAY-E2E-01',
            'invoice_id' => $invoice->id,
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'amount' => 59000.00,
            'payment_method' => 'bank_transfer',
            'provider_payment_id' => 'TXN-BANK-12345',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $invoice->update([
            'amount_paid' => 59000.00,
            'amount_due' => 0.00,
            'status' => InvoiceStatus::PAID,
        ]);

        // 6. Client views receipt
        $viewReceiptRes = $this->actingAs($this->clientA)
            ->get(route('client.payments.show', $payment->id));
        $viewReceiptRes->assertOk();

        // 7. Client sends Project Message
        $sendMessageRes = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA->id), [
                'message' => 'We have sent the deposit payment. Ready for development kickoff!',
            ]);
        $sendMessageRes->assertRedirect(route('client.projects.messages', $this->projectA->id));

        // 8. Staff uploads Client-Visible Document
        $file = UploadedFile::fake()->create('architecture_spec.pdf', 200, 'application/pdf');
        $uploadDocRes = $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientA->id,
                'project_id' => $this->projectA->id,
                'document_type' => 'other',
                'visibility' => DocumentVisibility::CLIENT->value,
                'file' => $file,
            ]);
        $uploadDocRes->assertRedirect();

        $doc = Document::where('project_id', $this->projectA->id)->firstOrFail();

        // 9. Client downloads Document securely
        $downloadDocRes = $this->actingAs($this->clientA)
            ->get(route('client.documents.download', $doc->id));
        $downloadDocRes->assertOk();

        // 10. Staff requests Client Review
        $requestReviewRes = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA->id), [
                'review_notes' => 'Please review the staging build.',
            ]);
        $requestReviewRes->assertRedirect(route('admin.projects.review', $this->projectA->id));
        $this->assertEquals(ProjectStatus::CLIENT_REVIEW, $this->projectA->fresh()->status);

        // 11. Client submits Feedback
        $feedbackRes = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.feedback', $this->projectA->id), [
                'feedback' => 'Please tweak primary color contrast.',
            ]);
        $feedbackRes->assertRedirect(route('client.projects.review', $this->projectA->id));

        // 12. Staff re-requests Review after revision
        $reRequestRes = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA->id), [
                'review_notes' => 'Color contrast updated.',
            ]);
        $reRequestRes->assertRedirect(route('admin.projects.review', $this->projectA->id));

        // 13. Client approves sign-off
        $approveRes = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.approve', $this->projectA->id));
        $approveRes->assertRedirect(route('client.projects.review', $this->projectA->id));

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertEquals(ProjectSignOffStatus::APPROVED, $signOff->status);

        // 14. Staff records Final Delivery & marks Project Completed
        $deliverRes = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.deliver', $this->projectA->id), [
                'final_delivery_notes' => 'Handover complete.',
            ]);
        $deliverRes->assertRedirect(route('admin.projects.review', $this->projectA->id));

        $this->assertEquals(ProjectStatus::COMPLETED, $this->projectA->fresh()->status);
    }

    /**
     * Comprehensive Cross-Client Isolation Audit across ALL resources.
     */
    public function test_comprehensive_cross_client_resource_isolation_audit()
    {
        // Setup Client B resources
        $quotationB = Quotation::create([
            'reference_number' => 'STZ-QT-B',
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
            'title' => 'Beta Proposal',
            'status' => QuotationStatus::SENT,
            'total' => 30000.00,
        ]);

        $invoiceB = Invoice::create([
            'reference_number' => 'STZ-INV-B',
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'status' => InvoiceStatus::ISSUED,
            'total' => 30000.00,
            'amount_due' => 30000.00,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $paymentB = Payment::create([
            'reference_number' => 'STZ-PAY-B',
            'invoice_id' => $invoiceB->id,
            'client_id' => $this->clientB->id,
            'amount' => 10000.00,
            'payment_method' => 'bank_transfer',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $docB = Document::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'uploaded_by_id' => $this->admin->id,
            'original_filename' => 'beta_secret.pdf',
            'storage_path' => 'documents/beta_secret.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'visibility' => DocumentVisibility::CLIENT,
            'document_type' => \App\Enums\DocumentType::OTHER,
        ]);

        $ticketB = SupportTicket::create([
            'client_id' => $this->clientB->id,
            'subject' => 'Beta Support Request',
            'description' => 'Beta issue',
            'category' => 'general',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        // Client A attempts to access Client B's resources
        $this->actingAs($this->clientA);

        // 1. Project Workspace
        $this->get(route('client.projects.show', $this->projectB->id))->assertForbidden();

        // 2. Project Activity Timeline
        $this->get(route('client.projects.activity', $this->projectB->id))->assertForbidden();

        // 3. Project Messages
        $this->get(route('client.projects.messages', $this->projectB->id))->assertForbidden();

        // 4. Project Review & Sign-Off
        $this->get(route('client.projects.review', $this->projectB->id))->assertForbidden();

        // 5. Quotation Show & Accept
        $this->get(route('client.quotations.show', $quotationB->id))->assertForbidden();
        $this->post(route('client.quotations.accept', $quotationB->id))->assertForbidden();

        // 6. Invoice Show
        $this->get(route('client.invoices.show', $invoiceB->id))->assertForbidden();

        // 7. Payment / Receipt Show
        $this->get(route('client.payments.show', $paymentB->id))->assertForbidden();

        // 8. Document Download
        $this->get(route('client.documents.download', $docB->id))->assertForbidden();

        // 9. Support Ticket Show
        $this->get(route('client.tickets.show', $ticketB->id))->assertForbidden();
    }

    /**
     * Activity privacy check: internal actions remain hidden from client.
     */
    public function test_activity_privacy_internal_actions_remain_hidden_from_client()
    {
        // Create an internal activity log
        ActivityLog::create([
            'actor_id' => $this->admin->id,
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'action' => 'ticket.internal_note_added',
            'description' => 'Staff added internal note: confidential client assessment',
        ]);

        // Client A views activity timeline
        $response = $this->actingAs($this->clientA)
            ->get(route('client.activity.index'));

        $response->assertOk();
        $response->assertDontSee('confidential client assessment');
    }
}
