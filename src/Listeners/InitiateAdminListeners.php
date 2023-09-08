<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Controllers\AdminpermissionroleController;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Enum\PermissionRoleEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Listeners\Admin\AdminMenuListener;
use VitesseCms\User\Listeners\Admin\AdminMenuPermissionListener;
use VitesseCms\User\Listeners\Controllers\AdminpermissionroleControllerListener;
use VitesseCms\User\Listeners\Controllers\AdminuserControllerListener;
use VitesseCms\User\Listeners\Models\PermissionRoleListener;
use VitesseCms\User\Listeners\Services\AclServiceListener;
use VitesseCms\User\Repositories\PermissionRoleRepository;
use VitesseCms\User\Repositories\UserRepository;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if (UserRoleEnum::isSuperAdmin($di->user->getPermissionRole())) :
            $di->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        $di->eventsManager->attach(AdminuserController::class, new AdminuserControllerListener(
            new PermissionRoleRepository(),
            $di->user,
            $di->flash,
            $di->request,
            $di->security,
            $di->setting,
            $di->eventsManager,
            new DatagroupRepository(),
            new DatafieldRepository()
        ));
        $di->eventsManager->attach(AdminpermissionroleController::class, new AdminpermissionroleControllerListener());
        $di->eventsManager->attach(AclEnum::SERVICE_LISTENER->value, new AclServiceListener($di->acl));
        $di->eventsManager->attach(UserEnum::SERVICE_LISTENER->value, new UserListener($di->user, new UserRepository()));
        $di->eventsManager->attach(PermissionRoleEnum::LISTENER->value, new PermissionRoleListener(
            new PermissionRoleRepository()
        ));
    }
}
