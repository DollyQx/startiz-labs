<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::PROCESSING => 'bg-blue-100 text-blue-800 border-blue-200',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::FAILED => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
