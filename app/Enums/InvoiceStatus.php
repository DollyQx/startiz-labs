<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ISSUED => 'Issued',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 border border-slate-200',
            self::ISSUED => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::PARTIALLY_PAID => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::PAID => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::OVERDUE => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::CANCELLED => 'bg-slate-100 text-slate-500 border border-slate-200',
        };
    }
}
