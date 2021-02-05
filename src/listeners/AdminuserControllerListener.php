<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use Phalcon\Events\Event;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Models\User;

class AdminuserControllerListener
{
    public function beforeModelSave(Event $event, AdminuserController $controller, User $user): void {
        if (
            $controller->request->getPost('new_password')
            && !empty($this->$controller->getPost('new_password'))
            && $controller->user->getPermissionRole() === 'superadmin'
        ) :
            echo 'hier';
        die();
            $item->set('password',$controller->security->hash(
                $controller->request->getPost('new_password'))
            );
        endif;
    }
}
