<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any invoices.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive());
    }

    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive() && (int) $user->id === (int) $invoice->client_id);
    }

    /**
     * Determine whether the client user can pay the invoice.
     */
    public function pay(User $user, Invoice $invoice): bool
    {
        return $user->isClient() && $user->isActive() && (int) $user->id === (int) $invoice->client_id;
    }

    /**
     * Determine whether the user can create invoices.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can record a payment against the invoice.
     */
    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }
}
