<?php

namespace App\Enums;

enum PartnerStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::SUSPENDED => 'Suspended',
            self::REJECTED => 'Rejected',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::SUSPENDED => 'bg-slate-100 text-slate-800 border-slate-200',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
