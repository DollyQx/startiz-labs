<?php

namespace App\Enums;

enum DocumentType: string
{
    case REQUIREMENT = 'requirement';
    case PROPOSAL = 'proposal';
    case CONTRACT = 'contract';
    case INVOICE = 'invoice';
    case RECEIPT = 'receipt';
    case DELIVERABLE = 'deliverable';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::REQUIREMENT => 'Requirement Document',
            self::PROPOSAL => 'Proposal / Quote',
            self::CONTRACT => 'Contract / Agreement',
            self::INVOICE => 'Invoice',
            self::RECEIPT => 'Payment Receipt',
            self::DELIVERABLE => 'Project Deliverable',
            self::OTHER => 'Other Document',
        };
    }
}
