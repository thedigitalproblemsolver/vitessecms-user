<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

use VitesseCms\Core\AbstractEnum;

class UserRoleEnum extends AbstractEnum
{
    public const GUEST = 'guest';
    public const SUPER_ADMIN = 'superadmin';
}