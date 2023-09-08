<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum PermissionRoleEnum: string
{
    case LISTENER = 'PermissionRoleListener';
    case GET_REPOSITORY = 'PermissionRoleListener:getRepository';
}