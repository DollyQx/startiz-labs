<?php

namespace App\Policies;

use App\Models\ClientProfile;
use App\Models\User;

class ClientProfilePolicy
{
    /**
     * Determine whether the user can view any client profiles.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the specific client profile.
     */
    public function view(User $user, ClientProfile $clientProfile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isClient() && (int) $user->id === (int) $clientProfile->user_id;
    }

    /**
     * Determine whether the user can update the client profile.
     */
    public function update(User $user, ClientProfile $clientProfile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isClient() && (int) $user->id === (int) $clientProfile->user_id;
    }

    /**
     * Determine whether the user can delete the client profile.
     */
    public function delete(User $user, ClientProfile $clientProfile): bool
    {
        return $user->isAdmin();
    }
}
