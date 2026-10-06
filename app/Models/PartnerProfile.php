<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Enums\PartnerStatus;
use App\Enums\ReferralStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_code',
        'company_name',
        'phone',
        'website',
        'payout_method',
        'payout_details',
        'commission_rate',
        'status',
        'approved_at',
        'approved_by_id',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'status' => PartnerStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PartnerProfile $profile) {
            if (empty($profile->referral_code)) {
                $prefix = (string) config('partner.referral_prefix', 'STZ');
                $profile->referral_code = ReferenceNumberGenerator::generate('partner_profiles', $prefix, 'referral_code', 6);
            }
            if (! isset($profile->commission_rate)) {
                $profile->commission_rate = (float) config('partner.default_commission_rate', 20.00);
            }
            if (empty($profile->status)) {
                $profile->status = PartnerStatus::PENDING;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'partner_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'partner_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'partner_id');
    }

    public function isApproved(): bool
    {
        return $this->status === PartnerStatus::APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === PartnerStatus::PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === PartnerStatus::SUSPENDED;
    }

    public function isRejected(): bool
    {
        return $this->status === PartnerStatus::REJECTED;
    }

    public function referralUrl(): string
    {
        return url('/?ref=' . $this->referral_code);
    }

    public function totalEarnings(): float
    {
        return (float) $this->commissions()
            ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value, CommissionStatus::PAID->value])
            ->sum('commission_amount');
    }

    public function pendingCommission(): float
    {
        return (float) $this->commissions()
            ->where('status', CommissionStatus::PENDING->value)
            ->sum('commission_amount');
    }

    public function approvedCommission(): float
    {
        return (float) $this->commissions()
            ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value])
            ->sum('commission_amount');
    }

    public function paidCommission(): float
    {
        return (float) $this->commissions()
            ->where('status', CommissionStatus::PAID->value)
            ->sum('commission_amount');
    }

    public function totalBusinessGenerated(): float
    {
        return (float) $this->commissions()
            ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value, CommissionStatus::PAID->value])
            ->sum('base_amount');
    }

    public function activeLeadsCount(): int
    {
        return $this->referrals()
            ->whereIn('status', [ReferralStatus::NEW->value, ReferralStatus::CONTACTED->value])
            ->count();
    }

    public function convertedClientsCount(): int
    {
        return $this->referrals()
            ->where('status', ReferralStatus::CONVERTED->value)
            ->count();
    }
}
