<?php
declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Listeners\Admin\AdminMenuListener;
use VitesseCms\User\Listeners\Admin\AdminMenuPermissionListener;
use VitesseCms\User\Listeners\Services\AclServiceListener;
use VitesseCms\User\Repositories\UserRepository;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        if (UserRoleEnum::isSuperAdmin($injectable->user->getPermissionRole())) :
            $injectable->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        if ($injectable->user->hasAdminAccess()) :
            $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $injectable->eventsManager->attach(
            UserEnum::SERVICE_LISTENER->value,
            new UserListener($injectable->user, new UserRepository())
        );
        $injectable->eventsManager->attach(AclEnum::SERVICE_LISTENER->value, new AclServiceListener($injectable->acl));
    }
}
