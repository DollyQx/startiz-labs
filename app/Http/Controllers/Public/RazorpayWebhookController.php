<?php

namespace App\Http\Controllers\Public;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ProcessedWebhookEvent;
use App\Services\ActivityLogger;
use App\Services\RazorpayService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(protected RazorpayService $razorpayService)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signatureHeader = (string) $request->header('X-Razorpay-Signature');
        $eventId = (string) $request->header('x-razorpay-event-id');

        // Verify cryptographic webhook signature using RAW body
        if (!$this->razorpayService->verifyWebhookSignature($rawPayload, $signatureHeader)) {
            Log::warning('Razorpay Webhook: Invalid signature received.');
            return response()->json([
                'success' => false,
                'error' => 'Invalid webhook signature',
            ], 400);
        }

        $payload = json_decode($rawPayload, true);
        $eventType = $payload['event'] ?? 'unknown';
        $eventId = !empty($eventId) ? $eventId : ($payload['event_id'] ?? ('evt_' . md5($rawPayload)));

        // Idempotency Check: Prevent duplicate webhook event processing
        $existingEvent = ProcessedWebhookEvent::where('event_id', $eventId)->first();
        if ($existingEvent) {
            return response()->json([
                'success' => true,
                'message' => 'Duplicate webhook event ignored.',
            ]);
        }

        try {
            DB::transaction(function () use ($eventType, $payload) {
                switch ($eventType) {
                    case 'payment.captured':
                    case 'payment.authorized':
                        $this->handlePaymentCaptured($payload['payload']['payment']['entity'] ?? []);
                        break;
                    case 'payment.failed':
                        $this->handlePaymentFailed($payload['payload']['payment']['entity'] ?? []);
                        break;
                    default:
                        Log::info("Razorpay Webhook: Event '{$eventType}' ignored.");
                        break;
                }
            });

            // Mark event as processed
            ProcessedWebhookEvent::create([
                'event_id' => $eventId,
                'event_type' => $eventType,
                'provider' => 'razorpay',
                'payload' => $payload,
                'processed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Razorpay Webhook Processing Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function handlePaymentCaptured(array $paymentData): void
    {
        if (empty($paymentData)) {
            return;
        }

        $razorpayPaymentId = $paymentData['id'] ?? null;
        $razorpayOrderId = $paymentData['order_id'] ?? null;
        $amountPaise = $paymentData['amount'] ?? 0;
        $amountRupees = (float) ($amountPaise / 100);
        $notes = $paymentData['notes'] ?? [];
        $invoiceId = $notes['invoice_id'] ?? null;

        if (!$razorpayOrderId && !$invoiceId) {
            Log::warning('Razorpay Webhook: Missing order_id or invoice_id in payload.');
            return;
        }

        // Locate internal payment record
        $payment = null;
        if ($razorpayOrderId) {
            $payment = Payment::where('provider_order_id', $razorpayOrderId)->first();
        }

        if (!$payment && $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                if ($amountRupees <= 0 || $amountRupees > (float) $invoice->amount_due + 0.01) {
                    Log::warning("Razorpay Webhook: Payment amount ₹{$amountRupees} exceeds invoice balance due ₹{$invoice->amount_due}.");
                    return;
                }

                $payment = Payment::create([
                    'reference_number' => 'STZ-PAY-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client_id,
                    'project_id' => $invoice->project_id,
                    'amount' => $amountRupees,
                    'currency' => 'INR',
                    'payment_method' => 'razorpay',
                    'provider' => 'razorpay',
                    'provider_order_id' => $razorpayOrderId,
                    'provider_payment_id' => $razorpayPaymentId,
                    'status' => PaymentStatus::PENDING,
                ]);
            }
        }

        if (!$payment) {
            Log::warning("Razorpay Webhook: No matching payment or invoice found for Order ID '{$razorpayOrderId}'.");
            return;
        }

        // Amount verification: Compare payment amount with expected payment amount
        if (abs((float) $payment->amount - $amountRupees) > 0.01) {
            Log::warning("Razorpay Webhook: Amount mismatch for Payment #{$payment->id}. Expected: {$payment->amount}, Received: {$amountRupees}");
            return;
        }

        // Idempotency: skip if already marked as paid
        if ($payment->status === PaymentStatus::PAID) {
            return;
        }

        $payment->update([
            'provider_payment_id' => $razorpayPaymentId,
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $invoice = $payment->invoice;
        if ($invoice) {
            $invoice->recalculateTotals();

            ActivityLogger::log(
                action: 'payment.created',
                subject: $invoice,
                description: 'Webhook confirmed Razorpay payment of ₹' . number_format($amountRupees, 2) . ' (Ref: ' . $payment->reference_number . ')',
                metadata: [
                    'payment_id' => $payment->id,
                    'provider_payment_id' => $razorpayPaymentId,
                    'provider_order_id' => $razorpayOrderId,
                    'amount' => $amountRupees,
                    'source' => 'webhook',
                ]
            );
        }
    }

    protected function handlePaymentFailed(array $paymentData): void
    {
        if (empty($paymentData)) {
            return;
        }

        $razorpayOrderId = $paymentData['order_id'] ?? null;
        if (!$razorpayOrderId) {
            return;
        }

        $payment = Payment::where('provider_order_id', $razorpayOrderId)->first();
        if ($payment && $payment->status !== PaymentStatus::PAID) {
            $payment->update([
                'status' => PaymentStatus::FAILED,
            ]);

            Log::info("Razorpay Webhook: Marked payment #{$payment->id} as FAILED.");
        }
    }
}
