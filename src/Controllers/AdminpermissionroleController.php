<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\User\Forms\PermissionRoleForm;
use VitesseCms\User\Models\PermissionRole;

class AdminpermissionroleController extends AbstractAdminController
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = PermissionRole::class;
        $this->classForm = PermissionRoleForm::class;
        $this->listSortable = true;
        $this->listNestable = true;
        $this->listOrder = 'ordering';
    }
}
