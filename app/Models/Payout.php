<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'partner_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'processed_by_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PayoutStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payout $payout) {
            if (empty($payout->reference_number)) {
                $prefix = (string) config('partner.payout_prefix', 'STZ-PAYOUT');
                $payout->reference_number = ReferenceNumberGenerator::generate('payouts', $prefix);
            }
            if (empty($payout->currency)) {
                $payout->currency = config('partner.currency', 'INR');
            }
            if (empty($payout->status)) {
                $payout->status = PayoutStatus::COMPLETED;
            }
            if (empty($payout->processed_at)) {
                $payout->processed_at = now();
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerProfile::class, 'partner_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'payout_id');
    }
}
