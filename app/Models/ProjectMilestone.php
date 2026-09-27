<?php

namespace App\Models;

use App\Enums\MilestoneStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'amount',
        'sequence_order',
        'status',
        'due_date',
        'completed_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sequence_order' => 'integer',
            'status' => MilestoneStatus::class,
            'due_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'milestone_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'milestone_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'milestone_id');
    }
}
