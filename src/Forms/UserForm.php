<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractFormWithRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\User\Enum\UserRoleEnum;

class UserForm extends AbstractFormWithRepository
{
    public function buildForm(): FormWithRepositoryInterface
    {
        $this->addEmail('%CORE_EMAIL%', 'email', (new Attributes())->setRequired())
            ->addDropdown(
                '%ADMIN_ROLE%',
                'role',
                (new Attributes())->setRequired()
                    ->setOptions(
                        ElementHelper::modelIteratorToOptions($this->repositories->permissionRole->findAll())
                    )
            )->addToggle('%USER_PASSWORD_FORCED_RESET%', 'forcePasswordReset');

        if ($this->user->getPermissionRole() === UserRoleEnum::SUPER_ADMIN) :
            $this->addPassword('%USER_PASSWORD%', 'new_password');
        endif;

        if ($this->setting->has('USER_DATAGROUP_PERSONALINFORMATION')) :
            $datagroup = $this->repositories->datagroup->getById($this->setting->get('USER_DATAGROUP_PERSONALINFORMATION'));
            $this->addHtml('<br /><h2>' . $datagroup->getNameField() . '</h2>');
            $datagroup->buildItemForm($this);
        endif;

        $this->addSubmitButton('%CORE_SAVE%');

        return $this;
    }
}
