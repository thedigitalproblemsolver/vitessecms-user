<?php declare(strict_types=1);

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
    public static function setListeners(InjectableInterface $di): void
    {
        if (UserRoleEnum::isSuperAdmin($di->user->getPermissionRole())) :
            $di->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        if ($di->user->hasAdminAccess()) :
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $di->eventsManager->attach(UserEnum::SERVICE_LISTENER->value, new UserListener($di->user, new UserRepository()));
        $di->eventsManager->attach(AclEnum::SERVICE_LISTENER->value, new AclServiceListener($di->acl));
    }
}
