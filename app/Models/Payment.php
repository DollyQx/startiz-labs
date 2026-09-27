<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'receipt_number',
        'client_id',
        'project_id',
        'quotation_id',
        'invoice_id',
        'milestone_id',
        'amount',
        'currency',
        'payment_method',
        'provider',
        'provider_payment_id',
        'provider_order_id',
        'provider_signature',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            if (empty($payment->reference_number)) {
                $payment->reference_number = ReferenceNumberGenerator::generate('payments', 'STZ-PAY');
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    /**
     * Ensure a unique receipt number exists for this payment.
     */
    public function generateReceiptNumber(): string
    {
        if (empty($this->receipt_number)) {
            $this->receipt_number = ReferenceNumberGenerator::generate('payments', 'STZ-REC', 'receipt_number');
            $this->save();
        }

        return $this->receipt_number;
    }
}
