<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    /**
     * Determine whether the user can view any quotations.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive());
    }

    /**
     * Determine whether the user can view the specific quotation.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive() && (int) $user->id === (int) $quotation->client_id);
    }

    /**
     * Determine whether the user can accept the quotation.
     */
    public function accept(User $user, Quotation $quotation): bool
    {
        return $user->isClient() && $user->isActive() && (int) $user->id === (int) $quotation->client_id;
    }

    /**
     * Determine whether the user can reject the quotation.
     */
    public function reject(User $user, Quotation $quotation): bool
    {
        return $user->isClient() && $user->isActive() && (int) $user->id === (int) $quotation->client_id;
    }

    /**
     * Determine whether the user can create quotations.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the quotation.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the quotation.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin();
    }
}
