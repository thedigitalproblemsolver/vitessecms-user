<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\User\Controllers\AdminpermissionroleController;

class AdminpermissionroleControllerListener
{
    public function adminListFilter(Event $event, AdminpermissionroleController $controller, AdminlistFormInterface $form): string
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}