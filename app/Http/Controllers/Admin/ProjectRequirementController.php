<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RequirementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequirementRequest;
use App\Http\Requests\Admin\UpdateRequirementRequest;
use App\Models\Project;
use App\Models\ProjectRequirement;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectRequirementController extends Controller
{
    /**
     * Store a new requirement for the given project.
     */
    public function store(StoreRequirementRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $data['project_id'] = $project->id;
        if (empty($data['submitted_by_id'])) {
            $data['submitted_by_id'] = auth()->id();
        }

        $requirement = ProjectRequirement::create($data);

        ActivityLogger::log(
            action: 'requirement.created',
            subject: $requirement,
            description: "Created requirement '{$requirement->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'status' => $requirement->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Requirement '{$requirement->title}' created successfully.");
    }

    /**
     * Update an existing requirement for the given project.
     */
    public function update(UpdateRequirementRequest $request, Project $project, ProjectRequirement $requirement): RedirectResponse
    {
        if ((int) $requirement->project_id !== (int) $project->id) {
            abort(404, 'Requirement does not belong to this project.');
        }

        $oldStatus = $requirement->status->value;
        $requirement->update($request->validated());

        ActivityLogger::log(
            action: 'requirement.updated',
            subject: $requirement,
            description: "Updated requirement '{$requirement->title}' for project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'old_status' => $oldStatus,
                'new_status' => $requirement->status->value
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Requirement '{$requirement->title}' updated successfully.");
    }

    /**
     * Update only the status of a specific requirement.
     */
    public function updateStatus(Request $request, Project $project, ProjectRequirement $requirement): RedirectResponse
    {
        if ((int) $requirement->project_id !== (int) $project->id) {
            abort(404, 'Requirement does not belong to this project.');
        }

        $request->validate([
            'status' => ['required', 'string', Rule::enum(RequirementStatus::class)],
        ]);

        $oldStatusLabel = $requirement->status->label();
        $requirement->update(['status' => $request->input('status')]);
        $requirement->refresh();

        ActivityLogger::log(
            action: 'requirement.status_updated',
            subject: $requirement,
            description: "Changed requirement '{$requirement->title}' status from {$oldStatusLabel} to {$requirement->status->label()} on project {$project->reference_number}",
            metadata: [
                'project_id' => $project->id,
                'from' => $oldStatusLabel,
                'to' => $requirement->status->label()
            ]
        );

        return redirect()
            ->route('admin.projects.show', $project->id)
            ->with('success', "Requirement status updated to {$requirement->status->label()}.");
    }
}
