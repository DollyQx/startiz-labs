<?php

namespace App\Http\Requests\Admin;

use App\Enums\MilestoneStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilestoneRequest extends FormRequest
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
            'amount' => ['nullable', 'numeric', 'min:0'],
            'sequence_order' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', Rule::enum(MilestoneStatus::class)],
            'due_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
        ];
    }
}
