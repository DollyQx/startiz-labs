<?php

namespace Tests\Feature\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $this->client = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => 'active',
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
        ]);
    }

    public function test_guests_cannot_access_invoice_routes(): void
    {
        $response = $this->get(route('admin.invoices.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_client_users_cannot_access_admin_invoice_routes(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.invoices.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_invoices_index_page(): void
    {
        Invoice::factory()->count(3)->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.invoices.index'));

        $response->assertOk();
        $response->assertViewIs('admin.invoices.index');
        $response->assertViewHas('invoices');
    }

    public function test_admin_can_create_an_invoice(): void
    {
        $payload = [
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => 1000.00,
            'discount' => 100.00,
            'tax' => 180.00,
            'status' => InvoiceStatus::ISSUED->value,
            'notes' => 'Test commercial invoice notes.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), $payload);

        $invoice = Invoice::where('client_id', $this->client->id)->first();

        $this->assertNotNull($invoice);
        $response->assertRedirect(route('admin.invoices.show', $invoice->id));

        $this->assertEquals(1080.00, (float) $invoice->total);
        $this->assertEquals(0.00, (float) $invoice->amount_paid);
        $this->assertEquals(1080.00, (float) $invoice->amount_due);
        $this->assertEquals(InvoiceStatus::ISSUED, $invoice->status);

        // Verify activity log
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
            'action' => 'invoice.created',
        ]);
    }

    public function test_admin_can_create_invoice_from_quotation(): void
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'subtotal' => 5000.00,
            'discount' => 500.00,
            'tax' => 810.00,
            'total' => 5310.00,
        ]);

        $payload = [
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'quotation_id' => $quotation->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => 5000.00,
            'discount' => 500.00,
            'tax' => 810.00,
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), $payload);

        $invoice = Invoice::where('quotation_id', $quotation->id)->first();

        $this->assertNotNull($invoice);
        $response->assertRedirect(route('admin.invoices.show', $invoice->id));
        $this->assertEquals(5310.00, (float) $invoice->total);
    }

    public function test_cross_relationship_validation_prevents_mismatched_client_and_project(): void
    {
        $otherClient = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => 'active',
        ]);

        $payload = [
            'client_id' => $otherClient->id,
            'project_id' => $this->project->id, // Belongs to $this->client
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => 1000.00,
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), $payload);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_admin_can_record_manual_payment_against_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'subtotal' => 1000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 1000.00,
            'amount_paid' => 0.00,
            'amount_due' => 1000.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        // First partial payment
        $paymentPayload1 = [
            'amount' => 400.00,
            'payment_method' => 'bank_transfer',
            'paid_at' => now()->toDateTimeString(),
            'notes' => 'First partial payment via NEFT',
        ];

        $response1 = $this->actingAs($this->admin)
            ->post(route('admin.invoices.payments.store', $invoice->id), $paymentPayload1);

        $response1->assertRedirect(route('admin.invoices.show', $invoice->id));

        $invoice->refresh();
        $this->assertEquals(400.00, (float) $invoice->amount_paid);
        $this->assertEquals(600.00, (float) $invoice->amount_due);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID, $invoice->status);

        // Final remaining payment
        $paymentPayload2 = [
            'amount' => 600.00,
            'payment_method' => 'upi',
            'paid_at' => now()->toDateTimeString(),
            'notes' => 'Final settlement via GPay',
        ];

        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.invoices.payments.store', $invoice->id), $paymentPayload2);

        $response2->assertRedirect(route('admin.invoices.show', $invoice->id));

        $invoice->refresh();
        $this->assertEquals(1000.00, (float) $invoice->amount_paid);
        $this->assertEquals(0.00, (float) $invoice->amount_due);
        $this->assertEquals(InvoiceStatus::PAID, $invoice->status);

        // Verify payment activity logs
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
            'action' => 'payment.created',
        ]);
    }

    public function test_cannot_record_payment_exceeding_invoice_amount_due(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'subtotal' => 1000.00,
            'total' => 1000.00,
            'amount_paid' => 0.00,
            'amount_due' => 1000.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        $excessPayload = [
            'amount' => 1500.00,
            'payment_method' => 'bank_transfer',
            'paid_at' => now()->toDateTimeString(),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.payments.store', $invoice->id), $excessPayload);

        $response->assertSessionHasErrors(['amount']);
    }
}
