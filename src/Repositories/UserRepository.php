<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\User\Models\User;
use VitesseCms\User\Models\UserIterator;

class UserRepository
{
    public function getById(string $id, bool $hideUnpublished = true): ?User
    {
        User::setFindPublished($hideUnpublished);

        /** @var User $user */
        $user = User::findById($id);
        if ($user instanceof User):
            return $user;
        endif;

        return null;
    }

    public function getByEmail(string $email): ?User
    {
        User::setFindValue('email', $email);

        /** @var User $user */
        $user = User::findFirst();
        if ($user instanceof User):
            return $user;
        endif;

        return null;
    }

    public function getByPasswordResetToken(string $token): ?User
    {
        User::setFindValue('passwordReset.passwordResetToken', $token);

        /** @var User $user */
        $user = User::findFirst();
        if ($user instanceof User):
            return $user;
        endif;

        return null;
    }

    public function findAll(
        ?FindValueIterator $findValues = null,
        bool               $hideUnpublished = true,
        ?int               $limit = null,
        ?FindOrderIterator $findOrders = null
    ): UserIterator
    {
        User::setFindPublished($hideUnpublished);
        User::addFindOrder('name');
        if ($limit !== null) :
            User::setFindLimit($limit);
        endif;
        $this->parseFindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new UserIterator(User::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                User::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }

    protected function parseFindOrders(?FindOrderIterator $findOrders = null): void
    {
        if ($findOrders !== null) :
            while ($findOrders->valid()) :
                $findOrder = $findOrders->current();
                User::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            endwhile;
        endif;
    }
}
