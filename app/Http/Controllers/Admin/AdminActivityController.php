<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminActivityController extends Controller
{
    /**
     * Display a paginated audit directory of activity logs with multi-filters.
     */
    public function index(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $query = ActivityLog::with(['actor', 'project', 'client', 'subject']);

        // Filter by Search
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by Action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by Entity Type (subject_type)
        if ($entityType = $request->input('entity_type')) {
            $mappedType = match ($entityType) {
                'Project' => \App\Models\Project::class,
                'Quotation' => \App\Models\Quotation::class,
                'Invoice' => \App\Models\Invoice::class,
                'Payment' => \App\Models\Payment::class,
                'SupportTicket' => \App\Models\SupportTicket::class,
                'ChangeRequest' => \App\Models\ChangeRequest::class,
                'Document' => \App\Models\Document::class,
                'User' => \App\Models\User::class,
                default => $entityType,
            };
            $query->where('subject_type', $mappedType);
        }

        // Filter by Client ID
        if ($clientId = $request->input('client_id')) {
            $query->forClient((int) $clientId);
        }

        // Filter by Project ID
        if ($projectId = $request->input('project_id')) {
            $query->forProject((int) $projectId);
        }

        // Filter by Actor ID
        if ($actorId = $request->input('actor_id')) {
            $query->where('actor_id', (int) $actorId);
        }

        // Filter by Date Range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $activities = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Data for dropdowns
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        $actors = User::whereIn('role', [
            UserRole::SUPER_ADMIN->value,
            UserRole::ADMIN->value,
            UserRole::PROJECT_MANAGER->value,
            UserRole::SUPPORT->value,
            UserRole::DEVELOPER->value,
            UserRole::CLIENT->value,
        ])->orderBy('name')->get();

        $distinctActions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity.index', [
            'activities' => $activities,
            'clients' => $clients,
            'projects' => $projects,
            'actors' => $actors,
            'actions' => $distinctActions,
            'entityTypes' => [
                'Project' => 'Projects',
                'Quotation' => 'Quotations',
                'Invoice' => 'Invoices',
                'Payment' => 'Payments',
                'SupportTicket' => 'Support Tickets',
                'ChangeRequest' => 'Change Requests',
                'Document' => 'Documents',
                'User' => 'Users',
            ],
        ]);
    }

    /**
     * Display granular audit details for a specific activity record.
     */
    public function show(Request $request, ActivityLog $activityLog): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $activityLog->load(['actor', 'project', 'client', 'subject']);

        // Sanitize sensitive metadata if present
        $sanitizedMetadata = $this->sanitizeMetadata($activityLog->metadata);

        return view('admin.activity.show', [
            'activity' => $activityLog,
            'metadata' => $sanitizedMetadata,
        ]);
    }

    /**
     * Recursively sanitize sensitive keys in metadata array.
     */
    protected function sanitizeMetadata(?array $metadata): ?array
    {
        if (! $metadata) {
            return null;
        }

        $sensitiveKeys = ['password', 'token', 'secret', 'key', 'credit_card', 'api_key', 'authorization', 'signature'];

        $sanitized = [];
        foreach ($metadata as $key => $value) {
            $isSensitive = false;
            foreach ($sensitiveKeys as $sKey) {
                if (stripos((string) $key, $sKey) !== false) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $sanitized[$key] = '******** [REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeMetadata($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
