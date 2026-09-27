<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Services\RazorpayService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RazorpayPaymentController extends Controller
{
    public function __construct(protected RazorpayService $razorpayService)
    {
    }

    /**
     * Create Razorpay Order for a Client Invoice server-side.
     */
    public function createOrder(Request $request, Invoice $invoice): JsonResponse
    {
        Gate::authorize('pay', $invoice);

        $invoice->recalculateTotals();

        if (in_array($invoice->status->value, [InvoiceStatus::PAID->value, InvoiceStatus::CANCELLED->value])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot initiate payment for a fully paid or cancelled invoice.',
            ], 422);
        }

        $amountDue = (float) $invoice->amount_due;
        if ($amountDue <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice has zero outstanding balance.',
            ], 422);
        }

        try {
            $order = $this->razorpayService->createOrder($invoice, $amountDue);

            // Create or update internal pending payment record
            Payment::updateOrCreate(
                [
                    'invoice_id' => $invoice->id,
                    'provider_order_id' => $order['id'],
                ],
                [
                    'reference_number' => 'STZ-PAY-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'client_id' => $invoice->client_id,
                    'project_id' => $invoice->project_id,
                    'quotation_id' => $invoice->quotation_id,
                    'milestone_id' => $invoice->milestone_id,
                    'amount' => $amountDue,
                    'currency' => 'INR',
                    'payment_method' => 'razorpay',
                    'provider' => 'razorpay',
                    'status' => PaymentStatus::PENDING,
                    'notes' => 'Razorpay online payment order created by client.',
                ]
            );

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'key_id' => $this->razorpayService->getKeyId(),
                'amount' => $order['amount'], // in paise
                'currency' => 'INR',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->reference_number,
                'client_name' => $invoice->client?->name ?? 'Client',
                'client_email' => $invoice->client?->email ?? '',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Razorpay Order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify Razorpay Checkout Payment Signature server-side for Client.
     */
    public function verifyPayment(Request $request, Invoice $invoice): JsonResponse
    {
        Gate::authorize('pay', $invoice);

        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $orderId = $request->input('razorpay_order_id');
        $paymentId = $request->input('razorpay_payment_id');
        $signature = $request->input('razorpay_signature');

        // Locate internal payment record by order ID and invoice ID
        $payment = Payment::where('provider_order_id', $orderId)
            ->where('invoice_id', $invoice->id)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'No matching payment order found for this invoice.',
            ], 400);
        }

        // Verify cryptographic signature
        $isValid = $this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature);
        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed.',
            ], 400);
        }

        // Idempotency check: if already paid
        if ($payment->status === PaymentStatus::PAID) {
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified and recorded.',
                'invoice_status' => $invoice->fresh()->status->value,
            ]);
        }

        DB::transaction(function () use ($payment, $paymentId, $signature, $invoice) {
            $payment->update([
                'provider_payment_id' => $paymentId,
                'provider_signature' => $signature,
                'status' => PaymentStatus::PAID,
                'paid_at' => now(),
            ]);

            $invoice->recalculateTotals();

            // Notifications
            if ($invoice->client) {
                \App\Services\NotificationService::notifyUser(
                    $invoice->client,
                    'payment',
                    "Payment Successful: ₹" . number_format((float) $payment->amount, 2),
                    "Your online payment of ₹" . number_format((float) $payment->amount, 2) . " for Invoice {$invoice->reference_number} has been verified.",
                    route('client.invoices.show', $invoice->id),
                    $payment
                );
            }

            \App\Services\NotificationService::notifyRoles(
                [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::FINANCE],
                'payment',
                "Online Payment Received: ₹" . number_format((float) $payment->amount, 2),
                "Client " . ($invoice->client->name ?? 'Client') . " paid ₹" . number_format((float) $payment->amount, 2) . " online for Invoice {$invoice->reference_number}.",
                route('admin.invoices.show', $invoice->id),
                $payment
            );

            ActivityLogger::log(
                action: 'payment.created',
                subject: $invoice,
                description: 'Client completed online Razorpay payment of ₹' . number_format((float) $payment->amount, 2) . ' (Ref: ' . $payment->reference_number . ')',
                metadata: [
                    'payment_id' => $payment->id,
                    'provider_payment_id' => $paymentId,
                    'provider_order_id' => $payment->provider_order_id,
                    'amount' => (float) $payment->amount,
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment completed and verified successfully!',
            'invoice_status' => $invoice->fresh()->status->value,
        ]);
    }
}
