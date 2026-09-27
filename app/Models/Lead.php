<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'name',
        'email',
        'phone',
        'company_name',
        'source',
        'status',
        'assigned_user_id',
        'client_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Lead $lead) {
            if (empty($lead->reference_number)) {
                $lead->reference_number = ReferenceNumberGenerator::generate('leads', 'STZ-LEAD');
            }
        });
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }
}
