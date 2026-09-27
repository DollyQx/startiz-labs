<?php

namespace App\Models;

use App\Enums\ProjectSignOffStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectReviewIteration extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_sign_off_id',
        'project_id',
        'iteration_number',
        'status',
        'review_notes',
        'client_feedback',
        'revision_request',
        'submitted_at',
        'responded_at',
    ];

    protected $casts = [
        'status' => ProjectSignOffStatus::class,
        'submitted_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function signOff(): BelongsTo
    {
        return $this->belongsTo(ProjectSignOff::class, 'project_sign_off_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
