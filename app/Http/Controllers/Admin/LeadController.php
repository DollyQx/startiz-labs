<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeadRequest;
use App\Http\Requests\Admin\UpdateLeadRequest;
use App\Models\ActivityLog;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display a paginated list of requirement leads with search and pipeline status filter.
     */
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');

        $query = Lead::with(['assignedUser', 'client']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && in_array($status, array_column(LeadStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        $leads = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $statuses = LeadStatus::cases();

        return view('admin.leads.index', compact('leads', 'search', 'status', 'statuses'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create(): View
    {
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $staffUsers = User::whereIn('role', [UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value])->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $statuses = LeadStatus::cases();

        return view('admin.leads.create', compact('clients', 'staffUsers', 'services', 'industries', 'statuses'));
    }

    /**
     * Store a newly created lead in the database.
     */
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $lead = Lead::create($data);

        ActivityLogger::log(
            action: 'lead.created',
            subject: $lead,
            description: "Created lead inquiry {$lead->reference_number} for {$lead->name} ({$lead->company_name})",
            metadata: ['status' => $lead->status->value]
        );

        return redirect()
            ->route('admin.leads.show', $lead->id)
            ->with('success', "Lead {$lead->reference_number} created successfully.");
    }

    /**
     * Display comprehensive details for a specific lead.
     */
    public function show(Lead $lead): View
    {
        $lead->load(['assignedUser', 'client', 'quotations']);

        $activityLogs = ActivityLog::where('subject_type', Lead::class)
            ->where('subject_id', $lead->id)
            ->with('actor')
            ->orderByDesc('created_at')
            ->get();

        $statuses = LeadStatus::cases();
        $staffUsers = User::whereIn('role', [UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value])->orderBy('name')->get();

        ActivityLogger::log(
            action: 'lead.viewed',
            subject: $lead,
            description: "Viewed details for lead {$lead->reference_number}"
        );

        return view('admin.leads.show', compact('lead', 'activityLogs', 'statuses', 'staffUsers'));
    }

    /**
     * Show the form for editing an existing lead.
     */
    public function edit(Lead $lead): View
    {
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $staffUsers = User::whereIn('role', [UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value])->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $statuses = LeadStatus::cases();

        return view('admin.leads.edit', compact('lead', 'clients', 'staffUsers', 'services', 'industries', 'statuses'));
    }

    /**
     * Update the specified lead in the database.
     */
    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $oldStatus = $lead->status->value;
        $data = $request->validated();

        $lead->update($data);

        ActivityLogger::log(
            action: 'lead.updated',
            subject: $lead,
            description: "Updated lead information for {$lead->reference_number}",
            metadata: ['old_status' => $oldStatus, 'new_status' => $lead->status->value]
        );

        return redirect()
            ->route('admin.leads.show', $lead->id)
            ->with('success', "Lead {$lead->reference_number} updated successfully.");
    }

    /**
     * Update only the pipeline status of a specific lead.
     */
    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', Rule::enum(LeadStatus::class)],
        ]);

        $oldStatus = $lead->status->label();
        $newStatusValue = $request->input('status');

        $lead->update(['status' => $newStatusValue]);
        $lead->refresh();

        ActivityLogger::log(
            action: 'lead.status_updated',
            subject: $lead,
            description: "Transitioned lead {$lead->reference_number} status from {$oldStatus} to {$lead->status->label()}",
            metadata: ['from' => $oldStatus, 'to' => $lead->status->label()]
        );

        return redirect()
            ->route('admin.leads.show', $lead->id)
            ->with('success', "Lead {$lead->reference_number} status updated to {$lead->status->label()}.");
    }
}
