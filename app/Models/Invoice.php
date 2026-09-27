<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'project_id',
        'quotation_id',
        'milestone_id',
        'issue_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'amount_paid',
        'amount_due',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Invoice $invoice) {
            if (empty($invoice->reference_number)) {
                $invoice->reference_number = ReferenceNumberGenerator::generate('invoices', 'STZ-INV');
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

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    /**
     * Recalculate invoice financial totals, amount paid, amount due, and update status.
     */
    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->subtotal;
        $discount = (float) $this->discount;
        $tax = (float) $this->tax;

        $total = max(0, $subtotal - $discount + $tax);

        $amountPaid = (float) $this->payments()
            ->where('status', \App\Enums\PaymentStatus::PAID)
            ->sum('amount');

        $amountDue = max(0, $total - $amountPaid);

        $status = $this->status;
        if (!in_array($this->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED])) {
            if ($amountDue == 0 && $total > 0) {
                $status = InvoiceStatus::PAID;
            } elseif ($amountPaid > 0 && $amountDue > 0) {
                $status = InvoiceStatus::PARTIALLY_PAID;
            } elseif ($amountPaid == 0 && $this->due_date && $this->due_date->isPast() && $status !== InvoiceStatus::DRAFT) {
                $status = InvoiceStatus::OVERDUE;
            } else {
                $status = InvoiceStatus::ISSUED;
            }
        }

        $this->update([
            'total' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'status' => $status,
        ]);
    }
}
