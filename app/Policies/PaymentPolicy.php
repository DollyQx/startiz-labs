<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any payments.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive());
    }

    /**
     * Determine whether the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive() && (int) $user->id === (int) $payment->client_id);
    }

    /**
     * Determine whether the user can generate or view a receipt for the payment.
     */
    public function generateReceipt(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || ($user->isClient() && $user->isActive() && (int) $user->id === (int) $payment->client_id);
    }
}
