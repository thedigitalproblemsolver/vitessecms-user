<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Controllers\AdmincountryController;
use VitesseCms\User\Controllers\AdminuserController;
use VitesseCms\User\Models\PermissionRole;
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
            $item->set('password', $controller->security->hash(
                $controller->request->getPost('new_password'))
            );
        endif;

        if ($controller->setting->has('USER_DATAGROUP_PERSONALINFORMATION')) :
            $datagroup = Datagroup::findById(
                $controller->setting->get('USER_DATAGROUP_PERSONALINFORMATION')
            );
            foreach ($datagroup->_('datafields') as $datafieldObject) :
                /** @var Datafield $datafield */
                $datafield = Datafield::findById($datafieldObject['id']);
                if (is_object($datafield)) :
                    $controller->eventsManager->fire($datafield->getClass() . ':beforeSave', $user, $datafield);
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
            );

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
