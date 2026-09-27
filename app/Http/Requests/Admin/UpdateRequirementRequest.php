<?php

namespace App\Http\Requests\Admin;

use App\Enums\RequirementPriority;
use App\Enums\RequirementStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequirementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', Rule::enum(RequirementPriority::class)],
            'status' => ['required', 'string', Rule::enum(RequirementStatus::class)],
            'submitted_by_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
