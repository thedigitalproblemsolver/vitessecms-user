<?php

declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum AclEnum: string
{
    case SERVICE_LISTENER = 'AclListener';
    case ATTACH_SERVICE_LISTENER = 'AclListener:attach';
    case ACCESS_EDIT = 'edit';
    case ACCESS_COPY = 'copy';
    case ACCESS_DELETE = 'delete';
    case ACCESS_TOGGLE_PUBLISH = 'togglepublish';
}