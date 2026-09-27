<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\RequirementPriority;
use App\Enums\RequirementStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\ActivityLog;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects with search and server-side filters.
     */
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');
        $priority = $request->get('priority');
        $clientId = $request->get('client_id');

        $query = Project::with(['client', 'service', 'industry']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($status) && in_array($status, array_column(ProjectStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        if (!empty($priority) && in_array($priority, array_column(ProjectPriority::cases(), 'value'), true)) {
            $query->where('priority', $priority);
        }

        if (!empty($clientId) && is_numeric($clientId)) {
            $query->where('client_id', (int) $clientId);
        }

        $projects = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $statuses = ProjectStatus::cases();
        $priorities = ProjectPriority::cases();
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();

        return view('admin.projects.index', compact(
            'projects', 'search', 'status', 'priority', 'clientId',
            'statuses', 'priorities', 'clients'
        ));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request): View
    {
        $selectedClientId = $request->get('client_id');
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $statuses = ProjectStatus::cases();
        $priorities = ProjectPriority::cases();

        return view('admin.projects.create', compact(
            'clients', 'services', 'industries', 'statuses', 'priorities', 'selectedClientId'
        ));
    }

    /**
     * Store a newly created project in the database.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $project = Project::create($data);

        ActivityLogger::log(
            action: 'project.created',
            subject: $project,
            description: "Created project {$project->reference_number} ({$project->title})",
            metadata: [
                'project_id' => $project->id,
                'client_id' => $project->client_id,
                'status' => $project->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Project {$project->reference_number} created successfully.");
    }

    /**
     * Display comprehensive workspace detail for a specific project.
     */
    public function show(Project $project): View
    {
        $project->load([
            'client.clientProfile',
            'service',
            'industry',
            'requirements.submittedBy',
            'milestones',
            'tasks.assignedUser',
            'tasks.milestone',
        ]);

        $totalMilestones = $project->milestones->count();
        $completedMilestones = $project->milestones->where('status', MilestoneStatus::COMPLETED)->count();

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', TaskStatus::COMPLETED)->count();
        $openTasks = $project->tasks->where('status', '!=', TaskStatus::COMPLETED)->count();
        $overdueTasks = $project->tasks->filter(function ($t) {
            return $t->due_date && $t->due_date->isPast() && $t->status !== TaskStatus::COMPLETED;
        })->count();

        $progressPercentage = 0;
        if ($totalTasks > 0) {
            $progressPercentage = (int) round(($completedTasks / $totalTasks) * 100);
        } elseif ($totalMilestones > 0) {
            $progressPercentage = (int) round(($completedMilestones / $totalMilestones) * 100);
        }

        $activityLogs = ActivityLog::where(function ($q) use ($project) {
            $q->where('subject_type', Project::class)->where('subject_id', $project->id);
        })->orWhere(function ($q) use ($project) {
            $q->where('metadata->project_id', $project->id);
        })->with('actor')->orderByDesc('created_at')->get();

        $requirementStatuses = RequirementStatus::cases();
        $requirementPriorities = RequirementPriority::cases();
        $milestoneStatuses = MilestoneStatus::cases();
        $taskStatuses = TaskStatus::cases();
        $taskPriorities = TaskPriority::cases();
        $projectStatuses = ProjectStatus::cases();
        $projectPriorities = ProjectPriority::cases();

        $staffUsers = User::whereIn('role', [UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value])->orderBy('name')->get();
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();

        ActivityLogger::log(
            action: 'project.viewed',
            subject: $project,
            description: "Viewed project workspace {$project->reference_number}",
            metadata: ['project_id' => $project->id]
        );

        return view('admin.projects.show', compact(
            'project', 'totalMilestones', 'completedMilestones',
            'totalTasks', 'completedTasks', 'openTasks', 'overdueTasks',
            'progressPercentage', 'activityLogs',
            'requirementStatuses', 'requirementPriorities',
            'milestoneStatuses', 'taskStatuses', 'taskPriorities',
            'projectStatuses', 'projectPriorities', 'staffUsers', 'clients'
        ));
    }

    /**
     * Show the form for editing an existing project.
     */
    public function edit(Project $project): View
    {
        $clients = User::where('role', UserRole::CLIENT->value)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $statuses = ProjectStatus::cases();
        $priorities = ProjectPriority::cases();

        return view('admin.projects.edit', compact(
            'project', 'clients', 'services', 'industries', 'statuses', 'priorities'
        ));
    }

    /**
     * Update the specified project in the database.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $oldStatus = $project->status->value;
        $data = $request->validated();

        $project->update($data);

        ActivityLogger::log(
            action: 'project.updated',
            subject: $project,
            description: "Updated project details for {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'old_status' => $oldStatus,
                'new_status' => $project->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Project {$project->reference_number} updated successfully.");
    }

    /**
     * Update the lifecycle status of a specific project.
     */
    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', Rule::enum(ProjectStatus::class)],
        ]);

        $oldStatusLabel = $project->status->label();
        $newStatusValue = $request->input('status');

        $updateData = ['status' => $newStatusValue];
        if ($newStatusValue === ProjectStatus::COMPLETED->value && !$project->actual_completion_date) {
            $updateData['actual_completion_date'] = now();
        }

        $project->update($updateData);
        $project->refresh();

        ActivityLogger::log(
            action: 'project.status_updated',
            subject: $project,
            description: "Transitioned project {$project->reference_number} status from {$oldStatusLabel} to {$project->status->label()}",
            metadata: [
                'project_id' => $project->id,
                'from' => $oldStatusLabel,
                'to' => $project->status->label()
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Project {$project->reference_number} status updated to {$project->status->label()}.");
    }
}
