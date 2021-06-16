<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Enums\SettingEnum;
use VitesseCms\User\Models\User;

class AdminuserControllerListener
{
    public function beforeModelSave(Event $event, AdminuserController $controller, User $user): void
    {
        if (
            $controller->request->getPost('new_password')
            && !empty($this->$controller->getPost('new_password'))
            && $controller->user->getPermissionRole() === 'superadmin'
        ) :
            $user->set('password', $controller->security->hash($controller->request->getPost('new_password')));
        endif;

        if ($controller->setting->has(SettingEnum::USER_DATAGROUP_PERSONALINFORMATION)) :
            $datagroup = $controller->repositories->datagroup->getById(
                $controller->setting->get(SettingEnum::USER_DATAGROUP_PERSONALINFORMATION)
            );
            foreach ($datagroup->getDatafields() as $datafieldObject) :
                $datafield = $controller->repositories->datafield->getById($datafieldObject['id']);
                if ($datafield !== null) :
                    $controller->eventsManager->fire($datafield->getType() . ':beforeSave', $user, $datafield);
                endif;
            endforeach;
        endif;
    }

    public function adminListFilter(Event $event, AdminuserController $controller, AdminlistFormInterface $form): string
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        $form->addText('%CORE_EMAIL%', 'filter[email]')
            ->addDropdown(
                'User role',
                'filter[role]',
                (new Attributes())->setOptions(
                    ElementHelper::modelIteratorToOptions($controller->repositories->permissionRole->findAll())
                )
            )
        ;

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
