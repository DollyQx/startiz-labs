<?php

namespace App\Http\Requests\Admin;

use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'lead_id' => ['nullable', 'integer', Rule::exists('leads', 'id')],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'issue_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'string', Rule::enum(QuotationStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['nullable', 'integer', Rule::exists('services', 'id')],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0'],
            'items.*.discount' => ['nullable', 'numeric', 'gte:0'],
            'items.*.tax' => ['nullable', 'numeric', 'gte:0'],
            'items.*.sequence_order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            /** @var Quotation|null $quotation */
            $quotation = $this->route('quotation');
            if ($quotation) {
                // Restrict editing finalized/accepted/cancelled quotations unless reverting status
                if (in_array($quotation->status, [QuotationStatus::ACCEPTED, QuotationStatus::CANCELLED], true)) {
                    $newStatus = $this->input('status');
                    if ($newStatus === $quotation->status->value) {
                        $validator->errors()->add('status', 'Quotations in ' . $quotation->status->label() . ' status cannot be edited directly.');
                    }
                }
            }

            $clientId = $this->input('client_id');
            if ($clientId) {
                $client = User::find($clientId);
                if (! $client || $client->role !== UserRole::CLIENT) {
                    $validator->errors()->add('client_id', 'The selected user is not a valid client.');
                }
            }

            $projectId = $this->input('project_id');
            if ($projectId && $clientId) {
                $project = Project::find($projectId);
                if ($project && (int) $project->client_id !== (int) $clientId) {
                    $validator->errors()->add('project_id', 'The selected project does not belong to the selected client.');
                }
            }

            $leadId = $this->input('lead_id');
            if ($leadId && $clientId) {
                $lead = Lead::find($leadId);
                if ($lead && $lead->client_id && (int) $lead->client_id !== (int) $clientId) {
                    $validator->errors()->add('lead_id', 'The selected lead is associated with a different client.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one quotation line item is required.',
            'items.min' => 'At least one quotation line item is required.',
            'valid_until.after_or_equal' => 'The valid-until date must be on or after the issue date.',
        ];
    }
}
