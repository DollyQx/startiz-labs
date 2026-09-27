<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case TESTING = 'testing';
    case CLIENT_REVIEW = 'client_review';
    case DEPLOYMENT = 'deployment';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planning',
            self::APPROVED => 'Approved',
            self::IN_PROGRESS => 'In Progress',
            self::TESTING => 'Testing',
            self::CLIENT_REVIEW => 'Client Review',
            self::DEPLOYMENT => 'Deployment',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On Hold',
            self::CANCELLED => 'Cancelled',
        };
    }
}
