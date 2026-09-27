<?php

namespace App\Http\Requests\Admin;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\ProjectMilestone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $project = $this->route('project');
        $projectId = is_object($project) ? $project->id : $project;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::enum(TaskStatus::class)],
            'priority' => ['required', 'string', Rule::enum(TaskPriority::class)],
            'due_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'milestone_id' => [
                'nullable',
                'integer',
                'exists:project_milestones,id',
                function ($attribute, $value, $fail) use ($projectId) {
                    if ($value && $projectId) {
                        $exists = ProjectMilestone::where('id', $value)
                            ->where('project_id', $projectId)
                            ->exists();
                        if (!$exists) {
                            $fail('The selected milestone does not belong to this project.');
                        }
                    }
                },
            ],
        ];
    }
}
