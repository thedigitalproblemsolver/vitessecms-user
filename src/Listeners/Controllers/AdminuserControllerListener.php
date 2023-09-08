<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Controllers;

use Phalcon\Encryption\Security;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Http\Request;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Core\Services\FlashService;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Enum\SettingsEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class AdminuserControllerListener
{
    public function __construct(
        private readonly PermissionRoleRepository $permissionRoleRepository,
        private readonly User $currentUser,
        private readonly FlashService $flashService,
        private readonly Request $request,
        private readonly Security $security,
        private readonly SettingService $settingService,
        private readonly Manager $eventsManager,
        private readonly DatagroupRepository $datagroupRepository,
        private readonly DatafieldRepository $datafieldRepository
    )
    {
    }

    public function beforeSaveModel(Event $event, User $user): void
    {
        if (
            $this->request->hasPost('new_password')
            && !empty($this->request->getPost('new_password'))
            && $this->currentUser->getPermissionRole() === UserRoleEnum::SUPER_ADMIN->value
        ) :
            $user->setPassword($this->security->hash($this->request->getPost('new_password')));
        endif;

        if ($this->settingService->has(SettingsEnum::USER_DATAGROUP_PERSONALINFORMATION->name)) :
            $datagroup = $this->datagroupRepository->getById(
                $this->settingService->get(SettingsEnum::USER_DATAGROUP_PERSONALINFORMATION->name)
            );
            foreach ($datagroup->getDatafields() as $datafieldObject) :
                $datafield = $this->datafieldRepository->getById($datafieldObject['id']);
                if ($datafield !== null) :
                    $this->eventsManager->fire($datafield->getType() . ':beforeSave', $user, $datafield);
                endif;
            endforeach;
        endif;
    }

    public function adminListFilter(Event $event, AdminuserController $controller, AdminlistFormInterface $form): void
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        $form->addText('%CORE_EMAIL%', 'filter[email]')
            ->addDropdown(
                'User role',
                'filter[role]',
                (new Attributes())->setOptions(
                    ElementHelper::modelIteratorToOptions($this->permissionRoleRepository->findAll())
                )
            );
    }

    public function validateDeleteAction(Event $event, User $userModel): bool
    {
        if ((string)$this->currentUser->getId() === (string)$userModel->getId()) :
            $this->flashService->setError('USER_NOT_DELETE_YOURSELF');

            return false;
        endif;

        return true;
    }
}
