<?php

namespace App\Http\Requests\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRole::CLIENT->value);
                }),
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'quotation_id' => [
                'nullable',
                'integer',
                'exists:quotations,id',
            ],
            'milestone_id' => [
                'nullable',
                'integer',
                'exists:project_milestones,id',
            ],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(InvoiceStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $clientId = $this->input('client_id');
            $projectId = $this->input('project_id');
            $quotationId = $this->input('quotation_id');
            $milestoneId = $this->input('milestone_id');

            if ($clientId && $projectId) {
                $project = Project::find($projectId);
                if ($project && (string) $project->client_id !== (string) $clientId) {
                    $validator->errors()->add('project_id', 'The selected project does not belong to the chosen client.');
                }
            }

            if ($clientId && $quotationId) {
                $quotation = Quotation::find($quotationId);
                if ($quotation && (string) $quotation->client_id !== (string) $clientId) {
                    $validator->errors()->add('quotation_id', 'The selected quotation does not belong to the chosen client.');
                }
                if ($quotation && $projectId && $quotation->project_id && (string) $quotation->project_id !== (string) $projectId) {
                    $validator->errors()->add('quotation_id', 'The selected quotation is associated with a different project.');
                }
            }

            if ($projectId && $milestoneId) {
                $milestone = ProjectMilestone::find($milestoneId);
                if ($milestone && (string) $milestone->project_id !== (string) $projectId) {
                    $validator->errors()->add('milestone_id', 'The selected milestone does not belong to the chosen project.');
                }
            }
        });
    }
}
