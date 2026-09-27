<?php

namespace App\Enums;

enum ChangeRequestStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-800 border-amber-200',
            self::UNDER_REVIEW => 'bg-blue-50 text-blue-800 border-blue-200',
            self::APPROVED => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            self::REJECTED => 'bg-rose-50 text-rose-800 border-rose-200',
            self::CANCELLED => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function isCancellable(): bool
    {
        return $this === self::PENDING;
    }
}
