<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Listeners\Admin\AdminMenuListener;
use VitesseCms\User\Listeners\Admin\AdminMenuPermissionListener;
use VitesseCms\User\Repositories\UserRepository;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if ($di->user->getPermissionRole() === UserRoleEnum::SUPER_ADMIN) :
            $di->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        if ($di->user->hasAdminAccess()) :
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $di->eventsManager->attach(UserEnum::USER_LISTENER, new UserListener(
            $di->user,
            new UserRepository()
        ));
    }
}
