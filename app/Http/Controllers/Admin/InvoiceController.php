<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdateInvoiceRequest;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::with(['client.clientProfile', 'project', 'quotation', 'milestone'])
            ->latest('id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('clientProfile', function ($profileQuery) use ($search) {
                                $profileQuery->where('company_name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->input('date_to'));
        }

        $invoices = $query->paginate(15)->withQueryString();

        $clients = User::where('role', UserRole::CLIENT->value)
            ->orderBy('name')
            ->get();

        $statuses = InvoiceStatus::cases();

        return view('admin.invoices.index', compact('invoices', 'clients', 'statuses'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        $selectedClient = null;
        $selectedProject = null;
        $selectedQuotation = null;

        if ($request->filled('quotation_id')) {
            $selectedQuotation = Quotation::with(['client', 'project'])->find($request->input('quotation_id'));
            if ($selectedQuotation) {
                $selectedClient = $selectedQuotation->client;
                $selectedProject = $selectedQuotation->project;
            }
        } elseif ($request->filled('project_id')) {
            $selectedProject = Project::with('client')->find($request->input('project_id'));
            if ($selectedProject) {
                $selectedClient = $selectedProject->client;
            }
        } elseif ($request->filled('client_id')) {
            $selectedClient = User::find($request->input('client_id'));
        }

        $clients = User::where('role', UserRole::CLIENT->value)
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('title')->get();
        $quotations = Quotation::whereIn('status', ['sent', 'viewed', 'accepted'])->orderBy('reference_number')->get();
        $milestones = ProjectMilestone::orderBy('sequence_order')->get();
        $statuses = InvoiceStatus::cases();

        return view('admin.invoices.create', compact(
            'clients',
            'projects',
            'quotations',
            'milestones',
            'statuses',
            'selectedClient',
            'selectedProject',
            'selectedQuotation'
        ));
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validated();

        $subtotal = (float) $validated['subtotal'];
        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $total = max(0, $subtotal - $discount + $tax);

        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'] ?? null,
            'quotation_id' => $validated['quotation_id'] ?? null,
            'milestone_id' => $validated['milestone_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'amount_paid' => 0.00,
            'amount_due' => $total,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($invoice->client) {
            \App\Services\NotificationService::notifyUser(
                $invoice->client,
                'invoice',
                "New Invoice Issued: {$invoice->reference_number}",
                "Invoice {$invoice->reference_number} for ₹" . number_format((float) $total, 2) . " has been issued.",
                route('client.invoices.show', $invoice->id),
                $invoice
            );
        }

        ActivityLogger::log(
            action: 'invoice.created',
            subject: $invoice,
            description: "Created invoice {$invoice->reference_number} for total ₹" . number_format($total, 2),
            metadata: [
                'total' => $total,
                'status' => $invoice->status->value,
                'client_id' => $invoice->client_id,
            ]
        );

        return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', "Invoice {$invoice->reference_number} created successfully.");
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load([
            'client.clientProfile',
            'project.service',
            'quotation',
            'milestone',
            'payments' => function ($q) {
                $q->latest('id');
            },
        ]);

        $activityLogs = ActivityLog::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->with('actor')
            ->latest()
            ->get();

        $statuses = InvoiceStatus::cases();

        return view('admin.invoices.show', compact('invoice', 'activityLogs', 'statuses'));
    }

    public function edit(Invoice $invoice): View|RedirectResponse
    {
        $this->authorize('update', $invoice);

        if (in_array($invoice->status->value, [InvoiceStatus::PAID->value, InvoiceStatus::CANCELLED->value])) {
            return redirect()
                ->route('admin.invoices.show', $invoice->id)
                ->with('error', "Finalized invoices ({$invoice->status->label()}) cannot be modified directly.");
        }

        $clients = User::where('role', UserRole::CLIENT->value)
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('title')->get();
        $quotations = Quotation::orderBy('reference_number')->get();
        $milestones = ProjectMilestone::orderBy('sequence_order')->get();
        $statuses = InvoiceStatus::cases();

        return view('admin.invoices.edit', compact('invoice', 'clients', 'projects', 'quotations', 'milestones', 'statuses'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $validated = $request->validated();

        $subtotal = (float) $validated['subtotal'];
        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);

        $invoice->update([
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'] ?? null,
            'quotation_id' => $validated['quotation_id'] ?? null,
            'milestone_id' => $validated['milestone_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $invoice->recalculateTotals();

        ActivityLogger::log(
            action: 'invoice.updated',
            subject: $invoice,
            description: "Updated invoice details for {$invoice->reference_number}",
            metadata: [
                'total' => (float) $invoice->total,
                'status' => $invoice->status->value,
            ]
        );

        return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', "Invoice {$invoice->reference_number} updated successfully.");
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'status' => ['required', Rule::enum(InvoiceStatus::class)],
        ]);

        $oldStatus = $invoice->status->value;
        $newStatus = $request->input('status');

        $invoice->update([
            'status' => $newStatus,
        ]);

        $invoice->recalculateTotals();

        if ($oldStatus !== $newStatus && $invoice->client) {
            \App\Services\NotificationService::notifyUser(
                $invoice->client,
                'invoice',
                "Invoice Status Updated: {$invoice->reference_number}",
                "Invoice {$invoice->reference_number} status updated to " . InvoiceStatus::from($newStatus)->label() . ".",
                route('client.invoices.show', $invoice->id),
                $invoice
            );
        }

        ActivityLogger::log(
            action: 'invoice.status_updated',
            subject: $invoice,
            description: "Updated status of invoice {$invoice->reference_number} from {$oldStatus} to {$newStatus}",
            metadata: [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );

        return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', "Invoice status updated to " . InvoiceStatus::from($newStatus)->label() . ".");
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('recordPayment', $invoice);

        $validated = $request->validated();

        $payment = Payment::create([
            'client_id' => $invoice->client_id,
            'project_id' => $invoice->project_id,
            'quotation_id' => $invoice->quotation_id,
            'invoice_id' => $invoice->id,
            'milestone_id' => $invoice->milestone_id,
            'amount' => $validated['amount'],
            'currency' => 'INR',
            'payment_method' => $validated['payment_method'],
            'provider' => 'manual',
            'status' => PaymentStatus::PAID,
            'paid_at' => $validated['paid_at'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $invoice->recalculateTotals();

        if ($invoice->client) {
            \App\Services\NotificationService::notifyUser(
                $invoice->client,
                'payment',
                "Payment Received: ₹" . number_format((float) $payment->amount, 2),
                "Payment of ₹" . number_format((float) $payment->amount, 2) . " has been received for Invoice {$invoice->reference_number}.",
                route('client.invoices.show', $invoice->id),
                $payment
            );
        }

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::FINANCE],
            'payment',
            "Payment Recorded: ₹" . number_format((float) $payment->amount, 2),
            "Payment recorded for Invoice {$invoice->reference_number} (Client: " . ($invoice->client->name ?? 'Client') . ").",
            route('admin.invoices.show', $invoice->id),
            $payment
        );

        ActivityLogger::log(
            action: 'payment.created',
            subject: $invoice,
            description: "Recorded manual payment {$payment->reference_number} of ₹" . number_format((float) $payment->amount, 2) . " for invoice {$invoice->reference_number}",
            metadata: [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->reference_number,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
            ]
        );

        return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', "Payment of ₹" . number_format((float) $payment->amount, 2) . " recorded successfully.");
    }
}
