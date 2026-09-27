<?php

namespace App\Enums;

enum TicketCategory: string
{
    case GENERAL = 'general';
    case TECHNICAL = 'technical';
    case BILLING = 'billing';
    case PROJECT = 'project';
    case ACCOUNT = 'account';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'General Inquiry',
            self::TECHNICAL => 'Technical Support',
            self::BILLING => 'Billing & Invoices',
            self::PROJECT => 'Project Request',
            self::ACCOUNT => 'Account Management',
            self::OTHER => 'Other',
        };
    }
}
