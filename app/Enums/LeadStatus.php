<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case QUOTATION_SENT = 'quotation_sent';
    case NEGOTIATION = 'negotiation';
    case WON = 'won';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::QUOTATION_SENT => 'Quotation Sent',
            self::NEGOTIATION => 'Negotiation',
            self::WON => 'Won',
            self::LOST => 'Lost',
        };
    }
}
