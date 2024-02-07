<?php
declare(strict_types=1);

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
    public static function setListeners(InjectableInterface $injectable): void
    {
        if (UserRoleEnum::isSuperAdmin($injectable->user->getPermissionRole())) :
            $injectable->eventsManager->attach('adminMenu', new AdminMenuPermissionListener());
        endif;
        $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        $injectable->eventsManager->attach(
            AdminuserController::class,
            new AdminuserControllerListener(
                new PermissionRoleRepository(),
                $injectable->user,
                $injectable->flash,
                $injectable->request,
                $injectable->security,
                $injectable->setting,
                $injectable->eventsManager,
                new DatagroupRepository(),
                new DatafieldRepository()
            )
        );
        $injectable->eventsManager->attach(
            AdminpermissionroleController::class,
            new AdminpermissionroleControllerListener()
        );
        $injectable->eventsManager->attach(AclEnum::SERVICE_LISTENER->value, new AclServiceListener($injectable->acl));
        $injectable->eventsManager->attach(
            UserEnum::SERVICE_LISTENER->value,
            new UserListener($injectable->user, new UserRepository())
        );
        $injectable->eventsManager->attach(
            PermissionRoleEnum::LISTENER->value,
            new PermissionRoleListener(
                new PermissionRoleRepository()
            )
        );
    }
}
