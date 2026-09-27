<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    /**
     * Determine whether the user can view ticket directory.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSupportStaff() || ($user->isClient() && $user->isActive());
    }

    /**
     * Determine whether the user can view the specific ticket.
     */
    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($user->isSupportStaff()) {
            return true;
        }

        return $user->isClient() && $user->isActive() && (int) $user->id === (int) $ticket->client_id;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return $user->isClient() && $user->isActive();
    }

    /**
     * Determine whether the user can reply to the ticket.
     */
    public function reply(User $user, SupportTicket $ticket): bool
    {
        if ($user->isSupportStaff()) {
            return true;
        }

        if ($user->isClient() && $user->isActive() && (int) $user->id === (int) $ticket->client_id) {
            $statusValue = $ticket->status instanceof TicketStatus ? $ticket->status->value : (string) $ticket->status;
            return $statusValue !== TicketStatus::CLOSED->value;
        }

        return false;
    }

    /**
     * Determine whether the user can update status.
     */
    public function updateStatus(User $user, SupportTicket $ticket): bool
    {
        return $user->isSupportStaff();
    }

    /**
     * Determine whether the user can reassign staff.
     */
    public function assign(User $user, SupportTicket $ticket): bool
    {
        return $user->isSupportStaff();
    }

    /**
     * Determine whether the user can add an internal note.
     */
    public function addInternalNote(User $user, SupportTicket $ticket): bool
    {
        return $user->isSupportStaff();
    }
}
