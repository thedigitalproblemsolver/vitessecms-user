<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum UserRoleEnum: string
{
    case GUEST = 'guest';
    case SUPER_ADMIN = 'superadmin';
    case REGISTERED = 'registered';

    public static function isSuperAdmin(string $role): bool
    {
        return $role === self::SUPER_ADMIN->value;
    }
}