<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectTaskController extends Controller
{
    /**
     * Store a new task for the given project.
     */
    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $data['project_id'] = $project->id;

        if (($data['status'] ?? null) === TaskStatus::COMPLETED->value && empty($data['completed_date'])) {
            $data['completed_date'] = now();
        }

        $task = Task::create($data);

        ActivityLogger::log(
            action: 'task.created',
            subject: $task,
            description: "Created task '{$task->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'assigned_user_id' => $task->assigned_user_id,
                'milestone_id' => $task->milestone_id,
                'status' => $task->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Task '{$task->title}' created successfully.");
    }

    /**
     * Update an existing task for the given project.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        if ((int) $task->project_id !== (int) $project->id) {
            abort(404, 'Task does not belong to this project.');
        }

        $oldAssignedUserId = $task->assigned_user_id;
        $data = $request->validated();

        if (($data['status'] ?? null) === TaskStatus::COMPLETED->value && empty($data['completed_date'])) {
            $data['completed_date'] = now();
        }

        $task->update($data);

        if ((int) $oldAssignedUserId !== (int) $task->assigned_user_id) {
            ActivityLogger::log(
                action: 'task.reassigned',
                subject: $task,
                description: "Reassigned task '{$task->title}' on project {$project->reference_number}",
                metadata: [
                    'project_id' => $project->id,
                    'old_assigned_user_id' => $oldAssignedUserId,
                    'new_assigned_user_id' => $task->assigned_user_id
                ]
            );
        }

        ActivityLogger::log(
            action: 'task.updated',
            subject: $task,
            description: "Updated task '{$task->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'status' => $task->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Task '{$task->title}' updated successfully.");
    }

    /**
     * Update only the status of a specific task.
     */
    public function updateStatus(Request $request, Project $project, Task $task): RedirectResponse
    {
        if ((int) $task->project_id !== (int) $project->id) {
            abort(404, 'Task does not belong to this project.');
        }

        $request->validate([
            'status' => ['required', 'string', Rule::enum(TaskStatus::class)],
        ]);

        $oldStatusLabel = $task->status->label();
        $newStatusValue = $request->input('status');

        $updateData = ['status' => $newStatusValue];
        if ($newStatusValue === TaskStatus::COMPLETED->value && !$task->completed_date) {
            $updateData['completed_date'] = now();
        }

        $task->update($updateData);
        $task->refresh();

        ActivityLogger::log(
            action: 'task.status_updated',
            subject: $task,
            description: "Changed task '{$task->title}' status from {$oldStatusLabel} to {$task->status->label()} on project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'from' => $oldStatusLabel,
                'to' => $task->status->label()
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Task status updated to {$task->status->label()}.");
    }
}
