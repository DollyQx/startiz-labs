<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case PROJECT_MANAGER = 'project_manager';
    case DEVELOPER = 'developer';
    case FINANCE = 'finance';
    case SUPPORT = 'support';
    case CLIENT = 'client';

    public function isAdminRole(): bool
    {
        return in_array($this, [self::ADMIN, self::SUPER_ADMIN], true);
    }

    public function isClientRole(): bool
    {
        return $this === self::CLIENT;
    }

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::PROJECT_MANAGER => 'Project Manager',
            self::DEVELOPER => 'Developer',
            self::FINANCE => 'Finance',
            self::SUPPORT => 'Support',
            self::CLIENT => 'Client',
        };
    }
}
