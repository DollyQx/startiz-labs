<?php

namespace App\Http\Controllers\Client;

use App\Enums\ChangeRequestPriority;
use App\Enums\ChangeRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    /**
     * Display a paginated directory of the client's change requests.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ChangeRequest::class);

        $query = ChangeRequest::where('client_id', auth()->id())
            ->with(['project']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        return view('client.change_requests.index', [
            'changeRequests' => $changeRequests,
            'statuses' => ChangeRequestStatus::cases(),
            'priorities' => ChangeRequestPriority::cases(),
        ]);
    }

    /**
     * Render the change request creation form.
     */
    public function create(): View
    {
        Gate::authorize('create', ChangeRequest::class);

        $projects = Project::where('client_id', auth()->id())->get();

        return view('client.change_requests.create', [
            'projects' => $projects,
            'priorities' => ChangeRequestPriority::cases(),
        ]);
    }

    /**
     * Store a new change request with strict project ownership validation.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', ChangeRequest::class);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
            'priority' => ['required', 'string', 'in:' . implode(',', array_column(ChangeRequestPriority::cases(), 'value'))],
        ]);

        // Strict Server-Side IDOR Check: Ensure project belongs to authenticated client
        $project = Project::where('id', $validated['project_id'])
            ->where('client_id', auth()->id())
            ->first();

        if (! $project) {
            throw ValidationException::withMessages([
                'project_id' => 'The selected project is invalid or does not belong to your account.',
            ]);
        }

        $changeRequest = ChangeRequest::create([
            'client_id' => auth()->id(),
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'reason' => $validated['reason'] ?? null,
            'priority' => $validated['priority'],
            'status' => ChangeRequestStatus::PENDING,
        ]);

        ActivityLogger::log(
            'change_request.created',
            $changeRequest,
            "Submitted change request '{$changeRequest->title}' ({$changeRequest->reference_number}) for project '{$project->title}'"
        );

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::PROJECT_MANAGER],
            'change_request',
            "New Change Request: {$changeRequest->reference_number}",
            "Client " . (auth()->user()->name ?? 'Client') . " submitted change request: {$changeRequest->title}",
            route('admin.change-requests.show', $changeRequest->id),
            $changeRequest
        );

        return redirect()->route('client.change-requests.show', $changeRequest->id)
            ->with('status', "Change request {$changeRequest->reference_number} submitted successfully.");
    }

    /**
     * Display client workspace for a specific change request.
     */
    public function show(ChangeRequest $changeRequest): View
    {
        Gate::authorize('view', $changeRequest);

        $changeRequest->load(['project', 'client', 'reviewer']);

        return view('client.change_requests.show', [
            'changeRequest' => $changeRequest,
        ]);
    }

    /**
     * Cancel an eligible PENDING change request.
     */
    public function cancel(ChangeRequest $changeRequest): RedirectResponse
    {
        Gate::authorize('cancel', $changeRequest);

        if (! $changeRequest->status->isCancellable()) {
            return back()->with('error', 'Only pending change requests can be cancelled.');
        }

        $changeRequest->update([
            'status' => ChangeRequestStatus::CANCELLED,
        ]);

        ActivityLogger::log(
            'change_request.cancelled',
            $changeRequest,
            "Client cancelled change request '{$changeRequest->reference_number}'"
        );

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::PROJECT_MANAGER],
            'change_request',
            "Change Request Cancelled: {$changeRequest->reference_number}",
            "Client " . (auth()->user()->name ?? 'Client') . " cancelled change request {$changeRequest->reference_number}.",
            route('admin.change-requests.show', $changeRequest->id),
            $changeRequest
        );

        return back()->with('status', "Change request {$changeRequest->reference_number} was cancelled.");
    }
}
