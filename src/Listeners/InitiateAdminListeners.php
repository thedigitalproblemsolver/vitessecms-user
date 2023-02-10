<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\User\Controllers\AdminpermissionroleController;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Listeners\Admin\AdminMenuListener;
use VitesseCms\User\Listeners\Admin\AdminMenuPermissionListener;
use VitesseCms\User\Listeners\Controllers\AdminpermissionroleControllerListener;
use VitesseCms\User\Listeners\Controllers\AdminuserControllerListener;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if (UserRoleEnum::isSuperAdmin($di->user->getPermissionRole())) :
            $di->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        $di->eventsManager->attach(AdminuserController::class, new AdminuserControllerListener());
        $di->eventsManager->attach(AdminpermissionroleController::class, new AdminpermissionroleControllerListener());
    }
}
