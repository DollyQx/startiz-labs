<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_FOR_CLIENT = 'waiting_for_client';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::WAITING_FOR_CLIENT => 'Waiting for Client',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'bg-blue-50 text-blue-700 border-blue-200',
            self::IN_PROGRESS => 'bg-amber-50 text-amber-700 border-amber-200',
            self::WAITING_FOR_CLIENT => 'bg-purple-50 text-purple-700 border-purple-200',
            self::RESOLVED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CLOSED => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}
