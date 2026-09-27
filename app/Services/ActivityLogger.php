<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Record a new activity log entry with automatic project and client resolution.
     */
    public static function log(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $actorId = null
    ): ActivityLog {
        $user = auth()->user();
        $projectId = $metadata['project_id'] ?? null;
        $clientId = $metadata['client_id'] ?? null;

        if ($subject) {
            if ($subject instanceof Project) {
                $projectId = $projectId ?? $subject->id;
                $clientId = $clientId ?? $subject->client_id;
            } elseif ($subject instanceof User) {
                if ($subject->role === UserRole::CLIENT || (is_string($subject->role) && $subject->role === UserRole::CLIENT->value)) {
                    $clientId = $clientId ?? $subject->id;
                }
            } else {
                if (! $projectId && isset($subject->project_id)) {
                    $projectId = $subject->project_id;
                }
                if (! $clientId && isset($subject->client_id)) {
                    $clientId = $subject->client_id;
                }
                if (! $projectId && method_exists($subject, 'project') && $subject->relationLoaded('project')) {
                    $projectId = $subject->project?->id;
                }
                if (! $clientId && method_exists($subject, 'client') && $subject->relationLoaded('client')) {
                    $clientId = $subject->client?->id;
                }
            }
        }

        return ActivityLog::create([
            'actor_id' => $actorId ?? $user?->id,
            'project_id' => $projectId,
            'client_id' => $clientId,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
