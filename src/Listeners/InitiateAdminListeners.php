<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\User\Controllers\AdminpermissionroleController;
use VitesseCms\User\Controllers\AdminuserController;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdminuserController::class, new AdminuserControllerListener());
        $eventsManager->attach(AdminpermissionroleController::class, new AdminpermissionroleControllerListener());
    }
}
