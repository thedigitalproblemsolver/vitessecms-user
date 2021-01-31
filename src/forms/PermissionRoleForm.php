<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class PermissionRoleForm extends AbstractForm
{
    public function initialize()
    {
        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired()->setMultilang())
            ->addText('%ADMIN_CALLING_NAME%', 'calling_name', ( new Attributes())->setRequired())
            ->addToggle('%ADMIN_ADMINISTRATOR_RIGHTS%', 'adminAccess')
            ->addSubmitButton('%CORE_SAVE%')
        ;
    }
}
