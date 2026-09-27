<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MilestoneStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMilestoneRequest;
use App\Http\Requests\Admin\UpdateMilestoneRequest;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectMilestoneController extends Controller
{
    /**
     * Store a new milestone for the given project.
     */
    public function store(StoreMilestoneRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $data['project_id'] = $project->id;

        if (empty($data['sequence_order'])) {
            $data['sequence_order'] = ($project->milestones()->max('sequence_order') ?? 0) + 1;
        }

        if (($data['status'] ?? null) === MilestoneStatus::COMPLETED->value && empty($data['completed_date'])) {
            $data['completed_date'] = now();
        }

        $milestone = ProjectMilestone::create($data);

        ActivityLogger::log(
            action: 'milestone.created',
            subject: $milestone,
            description: "Created milestone '{$milestone->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'status' => $milestone->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Milestone '{$milestone->title}' created successfully.");
    }

    /**
     * Update an existing milestone for the given project.
     */
    public function update(UpdateMilestoneRequest $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404, 'Milestone does not belong to this project.');
        }

        $data = $request->validated();
        if (($data['status'] ?? null) === MilestoneStatus::COMPLETED->value && empty($data['completed_date'])) {
            $data['completed_date'] = now();
        }

        $oldStatus = $milestone->status->value;
        $milestone->update($data);

        ActivityLogger::log(
            action: 'milestone.updated',
            subject: $milestone,
            description: "Updated milestone '{$milestone->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'old_status' => $oldStatus,
                'new_status' => $milestone->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Milestone '{$milestone->title}' updated successfully.");
    }

    /**
     * Update only the status of a specific milestone.
     */
    public function updateStatus(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404, 'Milestone does not belong to this project.');
        }

        $request->validate([
            'status' => ['required', 'string', Rule::enum(MilestoneStatus::class)],
        ]);

        $oldStatusLabel = $milestone->status->label();
        $newStatusValue = $request->input('status');

        $updateData = ['status' => $newStatusValue];
        if ($newStatusValue === MilestoneStatus::COMPLETED->value && !$milestone->completed_date) {
            $updateData['completed_date'] = now();
        }

        $milestone->update($updateData);
        $milestone->refresh();

        ActivityLogger::log(
            action: 'milestone.status_updated',
            subject: $milestone,
            description: "Changed milestone '{$milestone->title}' status from {$oldStatusLabel} to {$milestone->status->label()} on project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'from' => $oldStatusLabel,
                'to' => $milestone->status->label()
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Milestone status updated to {$milestone->status->label()}.");
    }
}
