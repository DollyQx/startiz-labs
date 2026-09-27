<?php

namespace App\Models;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'service_id',
        'industry_id',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'estimated_value',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'priority' => ProjectPriority::class,
            'start_date' => 'date',
            'expected_completion_date' => 'date',
            'actual_completion_date' => 'date',
            'estimated_value' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project) {
            if (empty($project->reference_number)) {
                $project->reference_number = ReferenceNumberGenerator::generate('projects', 'STZ-PROJ');
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProjectRequirement::class, 'project_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id')->orderBy('sequence_order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'project_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'project_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'project_id');
    }

    public function conversation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProjectConversation::class, 'project_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ProjectMessage::class, 'project_id');
    }

    public function signOff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProjectSignOff::class, 'project_id');
    }

    public function reviewIterations(): HasMany
    {
        return $this->hasMany(ProjectReviewIteration::class, 'project_id')->orderBy('iteration_number', 'desc');
    }
}
