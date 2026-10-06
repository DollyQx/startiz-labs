<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'partner_id',
        'referral_id',
        'project_id',
        'invoice_id',
        'payment_id',
        'base_amount',
        'commission_rate',
        'commission_amount',
        'currency',
        'status',
        'payout_id',
        'notes',
        'rejection_reason',
        'approved_at',
        'approved_by_id',
        'payable_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'base_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'status' => CommissionStatus::class,
            'approved_at' => 'datetime',
            'payable_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Commission $commission) {
            if (empty($commission->reference_number)) {
                $prefix = (string) config('partner.commission_prefix', 'STZ-COM');
                $commission->reference_number = ReferenceNumberGenerator::generate('commissions', $prefix);
            }
            if (empty($commission->currency)) {
                $commission->currency = config('partner.currency', 'INR');
            }
            if (empty($commission->status)) {
                $commission->status = CommissionStatus::PENDING;
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerProfile::class, 'partner_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class, 'payout_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function isPending(): bool
    {
        return $this->status === CommissionStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === CommissionStatus::APPROVED;
    }

    public function isPayable(): bool
    {
        return $this->status === CommissionStatus::PAYABLE;
    }

    public function isPaid(): bool
    {
        return $this->status === CommissionStatus::PAID;
    }

    public function isRejected(): bool
    {
        return $this->status === CommissionStatus::REJECTED;
    }
}
