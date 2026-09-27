<?php

namespace Tests\Feature\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client1;
    private User $client2;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => \App\Enums\UserStatus::ACTIVE->value,
        ]);

        $this->client1 = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => \App\Enums\UserStatus::ACTIVE->value,
            'name' => 'Client One',
        ]);

        $this->client2 = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => \App\Enums\UserStatus::ACTIVE->value,
            'name' => 'Client Two',
        ]);

        $this->invoice = Invoice::create([
            'client_id' => $this->client1->id,
            'subtotal' => 1000.00,
            'tax_amount' => 180.00,
            'discount_amount' => 0.00,
            'total' => 1180.00,
            'amount_paid' => 0.00,
            'amount_due' => 1180.00,
            'status' => InvoiceStatus::ISSUED->value,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
        ]);
    }

    public function test_admin_can_view_payment_directory_and_filter_payments(): void
    {
        $payment = Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'provider_payment_id' => 'pay_test12345',
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index', [
            'search' => 'pay_test12345',
            'status' => 'paid',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Payment History');
        $response->assertSee($payment->reference_number);
        $response->assertSee('Client One');
        $response->assertSee('pay_test12345');
    }

    public function test_admin_can_view_payment_details(): void
    {
        $payment = Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'bank_transfer',
            'provider' => 'manual',
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
            'notes' => 'Manual wire transfer received in HDFC account',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.show', $payment->id));

        $response->assertStatus(200);
        $response->assertSee($payment->reference_number);
        $response->assertSee('₹500.00');
        $response->assertSee('Manual wire transfer received in HDFC account');
        $response->assertSee('Client One');
    }

    public function test_admin_can_generate_and_view_payment_receipt(): void
    {
        $payment = Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 1180.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'provider_payment_id' => 'pay_receipt_999',
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
        ]);

        $this->assertNull($payment->receipt_number);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.receipt', $payment->id));

        $response->assertStatus(200);
        $response->assertSee('OFFICIAL PAYMENT RECEIPT');
        $response->assertSee('Client One');
        $response->assertSee('₹1,180.00');

        $payment->refresh();
        $this->assertNotNull($payment->receipt_number);
        $this->assertStringStartsWith('STZ-REC-', $payment->receipt_number);
        $response->assertSee($payment->receipt_number);
    }

    public function test_failed_payment_cannot_generate_receipt(): void
    {
        $payment = Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'status' => PaymentStatus::FAILED->value,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.receipt', $payment->id));

        $response->assertStatus(403);
    }

    public function test_client_cannot_access_another_clients_payment_or_receipt(): void
    {
        $payment = Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
        ]);

        // Client 2 attempts to view Client 1's payment detail
        $responseShow = $this->actingAs($this->client2)->get(route('admin.payments.show', $payment->id));
        $responseShow->assertStatus(403);

        // Client 2 attempts to view Client 1's receipt
        $responseReceipt = $this->actingAs($this->client2)->get(route('admin.payments.receipt', $payment->id));
        $responseReceipt->assertStatus(403);
    }

    public function test_financial_dashboard_metrics_and_period_filters(): void
    {
        Payment::create([
            'client_id' => $this->client1->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'provider' => 'razorpay',
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['period' => 'this_month']));

        $response->assertStatus(200);
        $response->assertSee('Financial Summary');
        $response->assertSee('₹500.00');
    }
}
