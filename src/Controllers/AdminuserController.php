<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use VitesseCms\Admin\Interfaces\AdminModelAddableInterface;
use VitesseCms\Admin\Interfaces\AdminModelDeletableInterface;
use VitesseCms\Admin\Interfaces\AdminModelEditableInterface;
use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Admin\Interfaces\AdminModelListInterface;
use VitesseCms\Admin\Interfaces\AdminModelPublishableInterface;
use VitesseCms\Admin\Traits\TraitAdminModelAddable;
use VitesseCms\Admin\Traits\TraitAdminModelDeletable;
use VitesseCms\Admin\Traits\TraitAdminModelEditable;
use VitesseCms\Admin\Traits\TraitAdminModelList;
use VitesseCms\Admin\Traits\TraitAdminModelPublishable;
use VitesseCms\Core\AbstractControllerAdmin;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Forms\UserForm;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\UserRepository;

class AdminuserController extends AbstractControllerAdmin implements
    AdminModelDeletableInterface,
    AdminModelEditableInterface,
    AdminModelPublishableInterface,
    AdminModelListInterface,
    AdminModelAddableInterface
{
    use TraitAdminModelDeletable,
        TraitAdminModelAddable,
        TraitAdminModelEditable,
        TraitAdminModelPublishable,
        TraitAdminModelList;

    private readonly UserRepository $userRepository;

    public function onConstruct(): void
    {
        parent::OnConstruct();

        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY->value, new \stdClass());
    }

    public function deleteAction(string $id): void
    {
        echo 'deleteAction';
        die();
        /*if ($this->user->getId() !== $this->dispatcher->getParam(0)) :
            parent::deleteAction();
        else :
            $this->flash->setError('USER_NOT_DELETE_YOURSELF');
            $this->redirect($this->link . '/adminList');
        endif;*/
    }

    /*public function searchEmailAction(): void
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
    }*/
    public function getModel(string $id): ?AbstractCollection
    {
        return match ($id) {
            'new' => new User(),
            default => $this->userRepository->getById($id)
        };
    }

    public function getModelForm(): AdminModelFormInterface
    {
        return new UserForm();
    }

    public function getModelList(?FindValueIterator $findValueIterator): \ArrayIterator
    {
        return $this->userRepository->findAll(
            $findValueIterator,
            false,
            99999,
            new FindOrderIterator([new FindOrder('createdAt', -1)])
        );
    }
}
