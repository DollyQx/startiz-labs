<?php

namespace App\Policies;

use App\Enums\DocumentVisibility;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any documents.
     */
    public function viewAny(User $user): bool
    {
        if ($user->isClient()) {
            return $user->isActive();
        }

        return true;
    }

    /**
     * Determine whether the user can view the document detail.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->isClient()) {
            return $user->isActive()
                && (int) $document->client_id === (int) $user->id
                && $document->visibility === DocumentVisibility::CLIENT;
        }

        return true;
    }

    /**
     * Determine whether the user can upload/create documents.
     */
    public function create(User $user): bool
    {
        return ! $user->isClient();
    }

    /**
     * Determine whether the user can update the document (e.g. visibility/notes).
     */
    public function update(User $user, Document $document): bool
    {
        return ! $user->isClient();
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        return ! $user->isClient();
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, Document $document): bool
    {
        if ($user->isClient()) {
            return $user->isActive()
                && (int) $document->client_id === (int) $user->id
                && $document->visibility === DocumentVisibility::CLIENT;
        }

        return true;
    }
}
