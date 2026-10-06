<?php

namespace App\Enums;

enum CommissionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PAYABLE = 'payable';
    case PAID = 'paid';
    case REJECTED = 'rejected';

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isPayable(): bool
    {
        return in_array($this, [self::APPROVED, self::PAYABLE], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::PAYABLE => 'Payable',
            self::PAID => 'Paid',
            self::REJECTED => 'Rejected',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::APPROVED => 'bg-blue-100 text-blue-800 border-blue-200',
            self::PAYABLE => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::PAID => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
