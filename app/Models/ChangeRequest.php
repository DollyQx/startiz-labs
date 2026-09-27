<?php

namespace App\Models;

use App\Enums\ChangeRequestPriority;
use App\Enums\ChangeRequestStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'project_id',
        'client_id',
        'title',
        'description',
        'reason',
        'priority',
        'status',
        'scope_impact',
        'estimated_cost',
        'estimated_days',
        'client_notes',
        'admin_notes',
        'reviewed_by_id',
        'reviewed_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'status' => ChangeRequestStatus::class,
        'priority' => ChangeRequestPriority::class,
        'estimated_cost' => 'decimal:2',
        'estimated_days' => 'integer',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChangeRequest $changeRequest) {
            if (empty($changeRequest->reference_number)) {
                $changeRequest->reference_number = ReferenceNumberGenerator::generate(
                    'change_requests',
                    'STZ-CR'
                );
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }
}
