<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\User\Enum\PermissionRoleEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class UserForm extends AbstractForm implements AdminModelFormInterface
{
    private PermissionRoleRepository $permissionRoleRepository;
    private DatagroupRepository $datagroupRepository;
    private SettingService $settingService;

    public function __construct($entity = null, array $userOptions = [])
    {
        parent::__construct($entity, $userOptions);

        $this->permissionRoleRepository = $this->eventsManager->fire(PermissionRoleEnum::GET_REPOSITORY->value, new \stdClass());
        $this->settingService = $this->eventsManager->fire(SettingEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());
        $this->datagroupRepository = $this->eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new \stdClass());
    }

    public function buildForm(): void
    {
        $this->addEmail('%CORE_EMAIL%', 'email', (new Attributes())->setRequired())
            ->addDropdown(
                '%ADMIN_ROLE%',
                'role',
                (new Attributes())->setRequired()
                    ->setOptions(ElementHelper::modelIteratorToOptions($this->permissionRoleRepository->findAll()))
            )->addToggle('%USER_PASSWORD_FORCED_RESET%', 'forcePasswordReset');

        if ($this->user->getPermissionRole() === UserRoleEnum::SUPER_ADMIN->value) :
            $this->addPassword('%USER_PASSWORD%', 'new_password');
        endif;

        if ($this->settingService->has('USER_DATAGROUP_PERSONALINFORMATION')) :
            $datagroup = $this->datagroupRepository->getById($this->settingService->get('USER_DATAGROUP_PERSONALINFORMATION'));
            if ($datagroup !== null) {
                $this->addHtml('<br /><h2>' . $datagroup->getNameField() . '</h2>');
                $datagroup->buildItemForm($this);
            }
        endif;

        $this->addSubmitButton('%CORE_SAVE%');
    }
}
