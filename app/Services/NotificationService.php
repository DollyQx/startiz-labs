<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\GenericDatabaseNotification;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public const CATEGORIES = [
        'quotation',
        'invoice',
        'payment',
        'project',
        'support_ticket',
        'change_request',
        'document',
        'project_message',
        'system',
    ];

    /**
     * Send a database notification to a specific user.
     */
    public static function notifyUser(
        ?User $user,
        string $category,
        string $title,
        string $message,
        ?string $url = null,
        ?Model $entity = null
    ): void {
        if (! $user) {
            return;
        }

        $user->notify(new GenericDatabaseNotification(
            category: $category,
            title: $title,
            message: $message,
            url: $url,
            entityType: $entity ? get_class($entity) : null,
            entityId: $entity?->id
        ));
    }

    /**
     * Send a database notification to active users matching specified roles.
     *
     * @param array<int, mixed> $roles Enum cases or role string values
     */
    public static function notifyRoles(
        array $roles,
        string $category,
        string $title,
        string $message,
        ?string $url = null,
        ?Model $entity = null
    ): void {
        $roleValues = array_map(function ($role) {
            return is_object($role) && property_exists($role, 'value') ? $role->value : (string) $role;
        }, $roles);

        $users = User::whereIn('role', $roleValues)
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', \App\Enums\UserStatus::ACTIVE);
            })
            ->get();

        foreach ($users as $user) {
            self::notifyUser($user, $category, $title, $message, $url, $entity);
        }
    }
}
