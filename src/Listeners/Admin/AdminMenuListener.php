<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Admin;

use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;
use VitesseCms\Datagroup\Models\Datagroup;
use Phalcon\Events\Event;

class AdminMenuListener
{
    public function AddChildren(Event $event, AdminMenu $adminMenu): void
    {
        $group = $adminMenu->getGroups()->getByKey('user');
        if ($group !== null) :
            $children = new AdminMenuNavBarChildren();
            $children->addChild('User details', 'admin/user/adminuser/adminList');

            /** @var Datagroup $contentGroup */
            foreach ($group->getDatagroups() as $contentGroup) :
                $children->addChild($contentGroup->_('name'), 'admin/content/adminitem/adminList/?filter[datagroup]=' . $contentGroup->getId());
            endforeach;

            $children->addChild('Logout', 'user/index/logout');
            $adminMenu->addDropdown('Users', $children);
        endif;
    }
}
