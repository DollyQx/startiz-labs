<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    /**
     * Display a paginated listing of payments with search and filtering.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Payment::class);

        $query = Payment::with(['client', 'invoice', 'project']);

        // Search by reference number, receipt number, client name, invoice ref, or provider IDs
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhere('provider_payment_id', 'like', "%{$search}%")
                    ->orWhere('provider_order_id', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice', function ($iq) use ($search) {
                        $iq->where('reference_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Client Filter
        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        // Payment Method Filter
        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        // Provider Filter
        if ($provider = $request->input('provider')) {
            $query->where('provider', $provider);
        }

        // Date From Filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        // Date To Filter
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $clients = User::where('role', \App\Enums\UserRole::CLIENT->value)->orderBy('name')->get();
        $statuses = PaymentStatus::cases();
        $providers = Payment::select('provider')->whereNotNull('provider')->distinct()->pluck('provider');
        $methods = Payment::select('payment_method')->whereNotNull('payment_method')->distinct()->pluck('payment_method');

        return view('admin.payments.index', compact(
            'payments',
            'clients',
            'statuses',
            'providers',
            'methods'
        ));
    }

    /**
     * Display specified payment detail workspace.
     */
    public function show(Payment $payment): View
    {
        Gate::authorize('view', $payment);

        $payment->load(['client', 'project', 'quotation', 'invoice', 'milestone']);

        // Fetch activity logs related to this payment or invoice
        $activityLogs = ActivityLog::where(function ($q) use ($payment) {
            $q->where('subject_type', Payment::class)->where('subject_id', $payment->id);
        })->orWhere(function ($q) use ($payment) {
            if ($payment->invoice_id) {
                $q->where('subject_type', \App\Models\Invoice::class)->where('subject_id', $payment->invoice_id);
            }
        })->orderByDesc('created_at')->limit(10)->get();

        return view('admin.payments.show', compact('payment', 'activityLogs'));
    }

    /**
     * Display printable client-facing receipt.
     */
    public function receipt(Payment $payment): View
    {
        Gate::authorize('generateReceipt', $payment);

        if ($payment->status !== PaymentStatus::PAID) {
            abort(403, 'Receipt can only be generated for successfully completed payments.');
        }

        // Ensure collision-safe receipt number is generated
        $payment->generateReceiptNumber();

        $payment->load(['client', 'project', 'quotation', 'invoice.client', 'milestone']);

        ActivityLogger::log(
            action: 'payment.receipt_downloaded',
            subject: $payment,
            description: 'Generated/viewed payment receipt #' . $payment->receipt_number,
            metadata: [
                'receipt_number' => $payment->receipt_number,
                'amount' => (float) $payment->amount,
            ]
        );

        return view('admin.payments.receipt', compact('payment'));
    }
}
