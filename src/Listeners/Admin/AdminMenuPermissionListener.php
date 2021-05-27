<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Admin;

use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;
use Phalcon\Events\Event;

class AdminMenuPermissionListener
{
    public function AddChildren(Event $event, AdminMenu $adminMenu): void
    {
        $children = new AdminMenuNavBarChildren();
        $children->addChild('Permissions', 'admin/user/adminpermissions/adminList')
            ->addChild('Permissions Roles', 'admin/user/adminpermissionrole/adminList');
        $adminMenu->addDropdown('Permissions', $children);
    }
}
