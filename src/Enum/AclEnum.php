<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

use VitesseCms\Core\AbstractEnum;

class AclEnum extends AbstractEnum
{
    public const SERVICE_LISTENER = 'AclListener';
    public const ATTACH_SERVICE_LISTENER = 'AclListener:attach';
}