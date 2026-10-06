<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'partner_id',
        'referral_code',
        'client_id',
        'lead_id',
        'client_name',
        'client_email',
        'client_phone',
        'service_requested',
        'ip_address',
        'user_agent',
        'status',
        'converted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'converted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Referral $referral) {
            if (empty($referral->reference_number)) {
                $referral->reference_number = ReferenceNumberGenerator::generate('referrals', 'STZ-REF');
            }
            if (empty($referral->status)) {
                $referral->status = ReferralStatus::NEW;
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerProfile::class, 'partner_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'referral_id');
    }

    public function isConverted(): bool
    {
        return $this->status === ReferralStatus::CONVERTED;
    }

    /**
     * Obfuscate client email for partner privacy view.
     */
    public function maskedClientEmail(): string
    {
        if (empty($this->client_email)) {
            return '—';
        }

        $parts = explode('@', $this->client_email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(1, $len - 3)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Safe display name for partner view.
     */
    public function safeDisplayName(): string
    {
        if (! empty($this->client_name)) {
            return $this->client_name;
        }

        if ($this->client) {
            return $this->client->name;
        }

        return 'Lead #' . $this->id;
    }

    /**
     * Masked client name to protect client privacy in partner portal.
     */
    public function maskedClientName(): string
    {
        $name = $this->safeDisplayName();
        $parts = explode(' ', trim($name));
        if (count($parts) > 1) {
            $first = $parts[0];
            $lastInitial = substr(end($parts), 0, 1);
            return $first . ' ' . $lastInitial . '.';
        }
        return strlen($name) > 3 ? substr($name, 0, 3) . '***' : $name;
    }

    /**
     * Masked client phone to prevent unauthorized outreach.
     */
    public function maskedClientPhone(): string
    {
        if (empty($this->client_phone)) {
            return '—';
        }
        $digits = preg_replace('/\D/', '', $this->client_phone);
        if (strlen($digits) >= 6) {
            return substr($digits, 0, 2) . str_repeat('*', strlen($digits) - 4) . substr($digits, -2);
        }
        return '***-***';
    }
}
