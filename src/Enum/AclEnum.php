<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum AclEnum: string
{
    case SERVICE_LISTENER = 'AclListener';
    case ATTACH_SERVICE_LISTENER = 'AclListener:attach';
}