<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\User\Forms\UserForm;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\RepositoriesInterface;

class AdminuserController extends AbstractAdminController implements RepositoriesInterface
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = User::class;
        $this->classForm = UserForm::class;
        $this->listOrder = 'email';
    }

    public function deleteAction(): void
    {
        if ($this->user->getId() !== $this->dispatcher->getParam(0)) :
            parent::deleteAction();
        else :
            $this->flash->setError('USER_NOT_DELETE_YOURSELF');
            $this->redirect($this->link . '/adminList');
        endif;
    }

    public function searchEmailAction(): void
    {
        if ($this->request->isAjax()) :
            $users = $this->repositories->user->findAll(new FindValueIterator(
                [new FindValue('email', $this->request->get('search'), 'like')]
            ));

            $result = ['items' => []];
            while ($users->valid()) :
                $user = $users->current();
                $tmp = ['id' => (string)$user->getId(), 'name' => $user->getEmail()];
                $result['items'][] = $tmp;
                $users->next();
            endwhile;

            $this->response->setContentType('application/json', 'UTF-8');
            echo json_encode($result);
        endif;

        $this->view->disable();
    }
}
