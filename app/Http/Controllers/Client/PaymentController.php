<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of client's payments.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Payment::class);

        $client = $request->user();

        $query = Payment::where('client_id', $client->id)
            ->with(['invoice', 'project']);

        // Search by reference number, receipt number, or invoice reference
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhere('provider_payment_id', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($iq) use ($search) {
                        $iq->where('reference_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Method filter
        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        $payments = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $statuses = PaymentStatus::cases();

        return view('client.payments.index', compact('payments', 'statuses'));
    }

    /**
     * Display client payment detail workspace.
     */
    public function show(Payment $payment): View
    {
        Gate::authorize('view', $payment);

        $payment->load(['client', 'project', 'quotation', 'invoice', 'milestone']);

        return view('client.payments.show', compact('payment'));
    }

    /**
     * Generate or view official printable receipt for client payment.
     */
    public function receipt(Payment $payment): View
    {
        Gate::authorize('generateReceipt', $payment);

        if ($payment->status !== PaymentStatus::PAID) {
            abort(403, 'Receipt can only be generated for successfully completed payments.');
        }

        $payment->generateReceiptNumber();
        $payment->load(['client.clientProfile', 'project', 'quotation', 'invoice.client', 'milestone']);

        ActivityLogger::log(
            action: 'payment.receipt_downloaded',
            subject: $payment,
            description: 'Client viewed payment receipt #' . $payment->receipt_number,
            metadata: [
                'receipt_number' => $payment->receipt_number,
                'amount' => (float) $payment->amount,
            ]
        );

        return view('admin.payments.receipt', compact('payment'));
    }
}
