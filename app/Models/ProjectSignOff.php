<?php

namespace App\Models;

use App\Enums\ProjectSignOffStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectSignOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'status',
        'requested_by_id',
        'accepted_at',
        'accepted_by_id',
        'final_delivery_at',
        'final_delivery_by_id',
        'final_delivery_notes',
    ];

    protected $casts = [
        'status' => ProjectSignOffStatus::class,
        'accepted_at' => 'datetime',
        'final_delivery_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_id');
    }

    public function finalDeliveryBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'final_delivery_by_id');
    }

    public function iterations(): HasMany
    {
        return $this->hasMany(ProjectReviewIteration::class)->orderBy('iteration_number', 'desc');
    }

    public function currentIteration(): HasOne
    {
        return $this->hasOne(ProjectReviewIteration::class)->latestOfMany('iteration_number');
    }

    public function isApproved(): bool
    {
        return $this->status === ProjectSignOffStatus::APPROVED;
    }

    public function isPendingReview(): bool
    {
        return $this->status === ProjectSignOffStatus::PENDING_REVIEW;
    }

    public function isFeedbackRequired(): bool
    {
        return $this->status === ProjectSignOffStatus::FEEDBACK_REQUIRED || $this->status === ProjectSignOffStatus::REVISION_IN_PROGRESS;
    }
}
