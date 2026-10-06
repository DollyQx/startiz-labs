<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case CONVERTED = 'converted';
    case LOST = 'lost';

    public function isConverted(): bool
    {
        return $this === self::CONVERTED;
    }

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::CONVERTED => 'Converted',
            self::LOST => 'Lost',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::NEW => 'bg-blue-100 text-blue-800 border-blue-200',
            self::CONTACTED => 'bg-purple-100 text-purple-800 border-purple-200',
            self::CONVERTED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::LOST => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
