<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuotationRequest;
use App\Http\Requests\Admin\UpdateQuotationRequest;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuotationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quotation::class);

        $query = Quotation::with(['client.clientProfile', 'lead', 'project']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhereHas('clientProfile', function ($pq) use ($search) {
                             $pq->where('company_name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        if ($from = $request->input('issue_date_from')) {
            $query->whereDate('issue_date', '>=', $from);
        }

        if ($to = $request->input('issue_date_to')) {
            $query->whereDate('issue_date', '<=', $to);
        }

        $quotations = $query->latest('id')->paginate(15)->withQueryString();

        $clients = User::where('role', UserRole::CLIENT->value)
            ->orderBy('name')
            ->get();

        $statuses = QuotationStatus::cases();

        return view('admin.quotations.index', compact('quotations', 'clients', 'statuses'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Quotation::class);

        $clients = User::where('role', UserRole::CLIENT->value)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $leads = Lead::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        $services = Service::where('is_active', true)->orderBy('display_order')->get();

        $selectedClientId = $request->query('client_id');
        $selectedLeadId = $request->query('lead_id');
        $selectedProjectId = $request->query('project_id');

        return view('admin.quotations.create', compact(
            'clients',
            'leads',
            'projects',
            'services',
            'selectedClientId',
            'selectedLeadId',
            'selectedProjectId'
        ));
    }

    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        $this->authorize('create', Quotation::class);

        $validated = $request->validated();

        $quotation = DB::transaction(function () use ($validated) {
            $quotation = Quotation::create([
                'client_id' => $validated['client_id'],
                'lead_id' => $validated['lead_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'issue_date' => $validated['issue_date'],
                'valid_until' => $validated['valid_until'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            foreach ($validated['items'] as $index => $itemData) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'service_id' => $itemData['service_id'] ?? null,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0.00,
                    'tax' => $itemData['tax'] ?? 0.00,
                    'sequence_order' => $itemData['sequence_order'] ?? ($index + 1),
                ]);
            }

            $quotation->load('items');
            $quotation->recalculateTotals();

            ActivityLogger::log(
                action: 'quotation.created',
                subject: $quotation,
                description: "Created quotation {$quotation->reference_number}",
                metadata: [
                    'reference_number' => $quotation->reference_number,
                    'total' => $quotation->total,
                    'status' => $quotation->status->value,
                ]
            );

            return $quotation;
        });

        return redirect()->route('admin.quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->reference_number} created successfully.");
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $quotation->load([
            'client.clientProfile',
            'lead',
            'project',
            'items.service',
        ]);

        $activityLogs = ActivityLog::where('subject_type', Quotation::class)
            ->where('subject_id', $quotation->id)
            ->with('actor')
            ->latest()
            ->get();

        $statuses = QuotationStatus::cases();

        return view('admin.quotations.show', compact('quotation', 'activityLogs', 'statuses'));
    }

    public function edit(Quotation $quotation): View|RedirectResponse
    {
        $this->authorize('update', $quotation);

        if (in_array($quotation->status, [QuotationStatus::ACCEPTED, QuotationStatus::CANCELLED], true)) {
            return redirect()->route('admin.quotations.show', $quotation->id)
                ->with('error', "Quotations in {$quotation->status->label()} status cannot be edited directly.");
        }

        $quotation->load(['items.service', 'client', 'lead', 'project']);

        $clients = User::where('role', UserRole::CLIENT->value)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $leads = Lead::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        $services = Service::where('is_active', true)->orderBy('display_order')->get();

        return view('admin.quotations.edit', compact('quotation', 'clients', 'leads', 'projects', 'services'));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        $validated = $request->validated();

        DB::transaction(function () use ($quotation, $validated) {
            $quotation->update([
                'client_id' => $validated['client_id'],
                'lead_id' => $validated['lead_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'issue_date' => $validated['issue_date'],
                'valid_until' => $validated['valid_until'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            // Replace items
            $quotation->items()->delete();

            foreach ($validated['items'] as $index => $itemData) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'service_id' => $itemData['service_id'] ?? null,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0.00,
                    'tax' => $itemData['tax'] ?? 0.00,
                    'sequence_order' => $itemData['sequence_order'] ?? ($index + 1),
                ]);
            }

            $quotation->load('items');
            $quotation->recalculateTotals();

            ActivityLogger::log(
                action: 'quotation.updated',
                subject: $quotation,
                description: "Updated quotation {$quotation->reference_number}",
                metadata: [
                    'reference_number' => $quotation->reference_number,
                    'total' => $quotation->total,
                    'status' => $quotation->status->value,
                ]
            );
        });

        return redirect()->route('admin.quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->reference_number} updated successfully.");
    }

    public function updateStatus(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::enum(QuotationStatus::class)],
        ]);

        $oldStatus = $quotation->status;
        $newStatus = QuotationStatus::from($validated['status']);

        if ($oldStatus !== $newStatus) {
            $quotation->update(['status' => $newStatus]);

            if ($newStatus === QuotationStatus::SENT && $quotation->client) {
                \App\Services\NotificationService::notifyUser(
                    $quotation->client,
                    'quotation',
                    "New Quotation Issued: {$quotation->reference_number}",
                    "A new quotation for ₹" . number_format((float) $quotation->total, 2) . " has been issued for your review.",
                    route('client.quotations.show', $quotation->id),
                    $quotation
                );
            }

            ActivityLogger::log(
                action: 'quotation.status_updated',
                subject: $quotation,
                description: "Transitioned quotation {$quotation->reference_number} status from {$oldStatus->label()} to {$newStatus->label()}",
                metadata: [
                    'from' => $oldStatus->value,
                    'to' => $newStatus->value,
                ]
            );
        }

        return redirect()->route('admin.quotations.show', $quotation->id)
            ->with('success', "Quotation status updated to {$newStatus->label()}.");
    }
}
