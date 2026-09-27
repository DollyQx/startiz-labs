<?php

namespace App\Policies;

use App\Models\ChangeRequest;
use App\Models\User;

class ChangeRequestPolicy
{
    /**
     * Determine whether the user can view any change requests.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specific change request.
     */
    public function view(User $user, ChangeRequest $changeRequest): bool
    {
        if ($user->isSupportStaff()) {
            return true;
        }

        return (int) $changeRequest->client_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create change requests.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the client user can cancel their change request.
     */
    public function cancel(User $user, ChangeRequest $changeRequest): bool
    {
        return (int) $changeRequest->client_id === (int) $user->id
            && $changeRequest->status->isCancellable();
    }

    /**
     * Determine whether staff can review the change request.
     */
    public function review(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->isSupportStaff();
    }

    /**
     * Determine whether staff can approve the change request.
     */
    public function approve(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->isSupportStaff();
    }

    /**
     * Determine whether staff can reject the change request.
     */
    public function reject(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->isSupportStaff();
    }
}
