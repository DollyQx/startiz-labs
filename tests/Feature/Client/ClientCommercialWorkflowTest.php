<?php

namespace Tests\Feature\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCommercialWorkflowTest extends TestCase
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
            'name' => 'Commercial Client Alpha',
            'email' => 'client.alpha@commercial.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Commercial Client Beta',
            'email' => 'client.beta@commercial.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha Commercial System',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Beta Enterprise App',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);
    }

    public function test_client_can_view_own_quotations_and_is_isolated_from_others(): void
    {
        $quoA = Quotation::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 1000.00,
            'discount' => 0.00,
            'tax' => 180.00,
            'total' => 1180.00,
            'status' => QuotationStatus::SENT,
        ]);

        $quoB = Quotation::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 5000.00,
            'discount' => 0.00,
            'tax' => 900.00,
            'total' => 5900.00,
            'status' => QuotationStatus::SENT,
        ]);

        // Client A index sees quoA but not quoB
        $this->actingAs($this->clientA)
            ->get(route('client.quotations.index'))
            ->assertStatus(200)
            ->assertSee($quoA->reference_number)
            ->assertDontSee($quoB->reference_number);

        // Client A show quoA -> 200 OK
        $this->actingAs($this->clientA)
            ->get(route('client.quotations.show', $quoA->id))
            ->assertStatus(200)
            ->assertSee($quoA->reference_number);

        // Client A show quoB -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.quotations.show', $quoB->id))
            ->assertStatus(403);
    }

    public function test_client_can_accept_eligible_quotation(): void
    {
        $quo = Quotation::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 2000.00,
            'discount' => 200.00,
            'tax' => 324.00,
            'total' => 2124.00,
            'status' => QuotationStatus::SENT,
        ]);

        $this->actingAs($this->clientA)
            ->post(route('client.quotations.accept', $quo->id))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertEquals(QuotationStatus::ACCEPTED, $quo->fresh()->status);
        $this->assertEquals(2124.00, (float) $quo->fresh()->total); // Totals preserved

        // Activity log created
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'quotation.accepted',
            'subject_type' => Quotation::class,
            'subject_id' => $quo->id,
        ]);
    }

    public function test_client_cannot_accept_already_accepted_or_cancelled_quotation(): void
    {
        $quo = Quotation::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 1000.00,
            'tax' => 180.00,
            'total' => 1180.00,
            'status' => QuotationStatus::CANCELLED,
        ]);

        $this->actingAs($this->clientA)
            ->post(route('client.quotations.accept', $quo->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertEquals(QuotationStatus::CANCELLED, $quo->fresh()->status);
    }

    public function test_client_can_reject_eligible_quotation(): void
    {
        $quo = Quotation::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 1500.00,
            'tax' => 270.00,
            'total' => 1770.00,
            'status' => QuotationStatus::SENT,
        ]);

        $this->actingAs($this->clientA)
            ->post(route('client.quotations.reject', $quo->id))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertEquals(QuotationStatus::REJECTED, $quo->fresh()->status);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'quotation.rejected',
            'subject_type' => Quotation::class,
            'subject_id' => $quo->id,
        ]);
    }

    public function test_client_can_view_own_invoices_and_is_isolated_from_others(): void
    {
        $invA = Invoice::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 3000.00,
            'tax' => 540.00,
            'total' => 3540.00,
            'amount_paid' => 0.00,
            'amount_due' => 3540.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        $invB = Invoice::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 4000.00,
            'tax' => 720.00,
            'total' => 4720.00,
            'amount_paid' => 0.00,
            'amount_due' => 4720.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        // Client A index
        $this->actingAs($this->clientA)
            ->get(route('client.invoices.index'))
            ->assertStatus(200)
            ->assertSee($invA->reference_number)
            ->assertDontSee($invB->reference_number);

        // Client A view invA -> 200 OK
        $this->actingAs($this->clientA)
            ->get(route('client.invoices.show', $invA->id))
            ->assertStatus(200)
            ->assertSee($invA->reference_number);

        // Client A view invB -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.invoices.show', $invB->id))
            ->assertStatus(403);
    }

    public function test_client_razorpay_order_creation_determines_amount_server_side_and_enforces_isolation(): void
    {
        $invA = Invoice::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 5000.00,
            'tax' => 900.00,
            'total' => 5900.00,
            'amount_paid' => 1000.00,
            'amount_due' => 4900.00,
            'status' => InvoiceStatus::PARTIALLY_PAID,
        ]);

        Payment::create([
            'reference_number' => 'STZ-PAY-PREV01',
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'invoice_id' => $invA->id,
            'amount' => 1000.00,
            'currency' => 'INR',
            'payment_method' => 'manual',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $invB = Invoice::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 1000.00,
            'total' => 1000.00,
            'amount_paid' => 0.00,
            'amount_due' => 1000.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        // Client A creates order for invA -> Order created for exact server amount (490000 paise)
        $res = $this->actingAs($this->clientA)
            ->postJson(route('client.invoices.razorpay.order', $invA->id))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount' => 490000,
            ]);

        $orderId = $res->json('order_id');
        $this->assertNotEmpty($orderId);

        // Client A attempts to create order for Client B's invoice -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->postJson(route('client.invoices.razorpay.order', $invB->id))
            ->assertStatus(403);

        // Paid invoice creation attempt -> 422
        $invPaid = Invoice::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 1000.00,
            'total' => 1000.00,
            'amount_paid' => 1000.00,
            'amount_due' => 0.00,
            'status' => InvoiceStatus::PAID,
        ]);

        Payment::create([
            'reference_number' => 'STZ-PAY-PAID01',
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'invoice_id' => $invPaid->id,
            'amount' => 1000.00,
            'currency' => 'INR',
            'payment_method' => 'manual',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($this->clientA)
            ->postJson(route('client.invoices.razorpay.order', $invPaid->id))
            ->assertStatus(422);
    }

    public function test_client_razorpay_payment_signature_verification_and_reconciliation(): void
    {
        $inv = Invoice::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 2000.00,
            'total' => 2000.00,
            'amount_paid' => 0.00,
            'amount_due' => 2000.00,
            'status' => InvoiceStatus::ISSUED,
        ]);

        // Create Order first
        $orderRes = $this->actingAs($this->clientA)
            ->postJson(route('client.invoices.razorpay.order', $inv->id))
            ->assertStatus(200);

        $orderId = $orderRes->json('order_id');

        // Generate HMAC signature for test key_secret
        $keySecret = (string) config('services.razorpay.key_secret', '');
        $paymentId = 'pay_test_' . rand(100000, 999999);
        $signature = hash_hmac('sha256', $orderId . '|' . $paymentId, $keySecret);

        // Verify payment
        $this->actingAs($this->clientA)
            ->postJson(route('client.invoices.razorpay.verify', $inv->id), [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'invoice_status' => 'paid',
            ]);

        // Check DB reconciliation
        $this->assertEquals(InvoiceStatus::PAID, $inv->fresh()->status);
        $this->assertEquals(2000.00, (float) $inv->fresh()->amount_paid);
        $this->assertEquals(0.00, (float) $inv->fresh()->amount_due);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $inv->id,
            'provider_order_id' => $orderId,
            'provider_payment_id' => $paymentId,
            'status' => PaymentStatus::PAID->value,
        ]);
    }

    public function test_client_can_view_own_payments_and_receipt_download_enforces_isolation(): void
    {
        $inv = Invoice::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 1000.00,
            'total' => 1000.00,
            'amount_paid' => 1000.00,
            'amount_due' => 0.00,
            'status' => InvoiceStatus::PAID,
        ]);

        $payA = Payment::create([
            'reference_number' => 'STZ-PAY-TEST01',
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'invoice_id' => $inv->id,
            'amount' => 1000.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $payB = Payment::create([
            'reference_number' => 'STZ-PAY-TEST02',
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'amount' => 2000.00,
            'currency' => 'INR',
            'payment_method' => 'manual',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        // Client A views payment history index
        $this->actingAs($this->clientA)
            ->get(route('client.payments.index'))
            ->assertStatus(200)
            ->assertSee($payA->reference_number)
            ->assertDontSee($payB->reference_number);

        // Client A views own payment show
        $this->actingAs($this->clientA)
            ->get(route('client.payments.show', $payA->id))
            ->assertStatus(200)
            ->assertSee($payA->reference_number);

        // Client A attempts viewing Client B's payment show -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.payments.show', $payB->id))
            ->assertStatus(403);

        // Client A downloads receipt for payA -> 200 OK
        $this->actingAs($this->clientA)
            ->get(route('client.payments.receipt', $payA->id))
            ->assertStatus(200)
            ->assertSee('OFFICIAL PAYMENT RECEIPT');

        // Client A attempts downloading receipt for payB -> 403 Forbidden
        $this->actingAs($this->clientA)
            ->get(route('client.payments.receipt', $payB->id))
            ->assertStatus(403);
    }
}
