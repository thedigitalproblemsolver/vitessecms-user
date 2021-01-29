<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;

class PermissionRoleForm extends AbstractForm
{
    public function initialize()
    {
        $this->_(
            'text',
            '%CORE_NAME%',
            'name',
            [
                'required' => 'required',
                'multilang' => true
            ]
        );
        $this->_(
            'text',
            '%ADMIN_CALLING_NAME%',
            'calling_name',
            ['required' => 'required']
        );
        $this->_(
            'checkbox',
            '%ADMIN_ADMINISTRATOR_RIGHTS%',
            'adminAccess'
        );
        $this->_(
            'submit',
            '%CORE_SAVE%'
        );
    }
}
