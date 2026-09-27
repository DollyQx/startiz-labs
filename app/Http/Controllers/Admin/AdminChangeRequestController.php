<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChangeRequestPriority;
use App\Enums\ChangeRequestStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminChangeRequestController extends Controller
{
    /**
     * Display a directory of all client change requests with multi-filters.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ChangeRequest::class);

        $query = ChangeRequest::with(['project', 'client', 'reviewer']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $projects = Project::orderBy('title')->get();

        return view('admin.change_requests.index', [
            'changeRequests' => $changeRequests,
            'clients' => $clients,
            'projects' => $projects,
            'statuses' => ChangeRequestStatus::cases(),
            'priorities' => ChangeRequestPriority::cases(),
        ]);
    }

    /**
     * Display administrative workspace for a specific change request.
     */
    public function show(ChangeRequest $changeRequest): View
    {
        Gate::authorize('view', $changeRequest);

        $changeRequest->load(['project', 'client', 'reviewer']);

        return view('admin.change_requests.show', [
            'changeRequest' => $changeRequest,
            'statuses' => ChangeRequestStatus::cases(),
        ]);
    }

    /**
     * Update scope impact, estimated cost, timeline, and staff notes.
     */
    public function updateReview(Request $request, ChangeRequest $changeRequest): RedirectResponse
    {
        Gate::authorize('review', $changeRequest);

        $validated = $request->validate([
            'scope_impact' => ['nullable', 'string'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_days' => ['nullable', 'integer', 'min:0'],
            'client_notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $updateData = [
            'scope_impact' => $validated['scope_impact'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'estimated_days' => $validated['estimated_days'] ?? null,
            'client_notes' => $validated['client_notes'] ?? null,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by_id' => auth()->id(),
            'reviewed_at' => now(),
        ];

        if ($changeRequest->status === ChangeRequestStatus::PENDING) {
            $updateData['status'] = ChangeRequestStatus::UNDER_REVIEW;
        }

        $changeRequest->update($updateData);

        ActivityLogger::log(
            'change_request.reviewed',
            $changeRequest,
            "Updated review & scope assessment for change request '{$changeRequest->reference_number}'"
        );

        if ($changeRequest->client) {
            \App\Services\NotificationService::notifyUser(
                $changeRequest->client,
                'change_request',
                "Change Request Under Review: {$changeRequest->reference_number}",
                "Your change request #{$changeRequest->reference_number} is now under review.",
                route('client.change-requests.show', $changeRequest->id),
                $changeRequest
            );
        }

        return back()->with('status', "Scope & cost assessment updated for {$changeRequest->reference_number}.");
    }

    /**
     * Approve the change request.
     */
    public function approve(ChangeRequest $changeRequest): RedirectResponse
    {
        Gate::authorize('approve', $changeRequest);

        if (in_array($changeRequest->status, [ChangeRequestStatus::APPROVED, ChangeRequestStatus::REJECTED, ChangeRequestStatus::CANCELLED], true)) {
            return back()->with('error', "Change request is already {$changeRequest->status->label()} and cannot be re-approved.");
        }

        $changeRequest->update([
            'status' => ChangeRequestStatus::APPROVED,
            'approved_at' => now(),
            'reviewed_by_id' => auth()->id(),
            'reviewed_at' => $changeRequest->reviewed_at ?? now(),
        ]);

        ActivityLogger::log(
            'change_request.approved',
            $changeRequest,
            "Approved change request '{$changeRequest->reference_number}'"
        );

        if ($changeRequest->client) {
            \App\Services\NotificationService::notifyUser(
                $changeRequest->client,
                'change_request',
                "Change Request Approved: {$changeRequest->reference_number}",
                "Your change request #{$changeRequest->reference_number} has been approved.",
                route('client.change-requests.show', $changeRequest->id),
                $changeRequest
            );
        }

        return back()->with('status', "Change request {$changeRequest->reference_number} has been APPROVED.");
    }

    /**
     * Reject the change request.
     */
    public function reject(ChangeRequest $changeRequest): RedirectResponse
    {
        Gate::authorize('reject', $changeRequest);

        if (in_array($changeRequest->status, [ChangeRequestStatus::APPROVED, ChangeRequestStatus::REJECTED, ChangeRequestStatus::CANCELLED], true)) {
            return back()->with('error', "Change request is already {$changeRequest->status->label()} and cannot be re-rejected.");
        }

        $changeRequest->update([
            'status' => ChangeRequestStatus::REJECTED,
            'rejected_at' => now(),
            'reviewed_by_id' => auth()->id(),
            'reviewed_at' => $changeRequest->reviewed_at ?? now(),
        ]);

        ActivityLogger::log(
            'change_request.rejected',
            $changeRequest,
            "Rejected change request '{$changeRequest->reference_number}'"
        );

        if ($changeRequest->client) {
            \App\Services\NotificationService::notifyUser(
                $changeRequest->client,
                'change_request',
                "Change Request Rejected: {$changeRequest->reference_number}",
                "Your change request #{$changeRequest->reference_number} was rejected.",
                route('client.change-requests.show', $changeRequest->id),
                $changeRequest
            );
        }

        return back()->with('status', "Change request {$changeRequest->reference_number} has been REJECTED.");
    }
}
