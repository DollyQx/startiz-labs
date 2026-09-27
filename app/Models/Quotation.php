<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'lead_id',
        'project_id',
        'issue_date',
        'valid_until',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'notes',
        'terms',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => QuotationStatus::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Quotation $quotation) {
            if (empty($quotation->reference_number)) {
                $quotation->reference_number = ReferenceNumberGenerator::generate('quotations', 'STZ-QUO');
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id')->orderBy('sequence_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'quotation_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'quotation_id');
    }

    /**
     * Recalculate quotation financial totals derived from items.
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $tax = $this->items->sum('tax');
        $discount = $this->items->sum('discount');
        $total = $subtotal - $discount + $tax;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => max(0, $total),
        ]);
    }
}
