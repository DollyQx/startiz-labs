<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::PROCESSING => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::PAID => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::FAILED => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::REFUNDED => 'bg-purple-50 text-purple-700 border border-purple-200',
            self::CANCELLED => 'bg-slate-100 text-slate-500 border border-slate-200',
        };
    }
}
