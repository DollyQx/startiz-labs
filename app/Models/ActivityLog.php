<?php

namespace App\Models;

use App\Services\ActivityVisibilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'project_id',
        'client_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope query to only client-visible activities.
     */
    public function scopeClientVisible(Builder $query): Builder
    {
        return ActivityVisibilityService::applyClientVisibilityScope($query);
    }

    /**
     * Scope query for a specific project.
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where(function ($q) use ($projectId) {
            $q->where('project_id', $projectId)
              ->orWhere(function ($sub) use ($projectId) {
                  $sub->where('subject_type', Project::class)->where('subject_id', $projectId);
              })
              ->orWhere('metadata->project_id', $projectId);
        });
    }

    /**
     * Scope query for a specific client.
     */
    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where(function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
              ->orWhere('metadata->client_id', $clientId);
        });
    }

    /**
     * Check if this activity log entry is safe for client viewing.
     */
    public function isClientVisible(): bool
    {
        return ActivityVisibilityService::isClientVisible($this);
    }

    /**
     * Accessor for human-readable action label.
     */
    public function getActionLabelAttribute(): string
    {
        return ActivityVisibilityService::getActionLabel($this->action);
    }
}
