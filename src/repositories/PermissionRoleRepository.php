<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\User\Models\PermissionRole;
use VitesseCms\User\Models\PermissionRoleIterator;

class PermissionRoleRepository
{
    public function getById(string $id, bool $hideUnpublished = true): ?PermissionRole
    {
        PermissionRole::setFindPublished($hideUnpublished);

        /** @var PermissionRole $permissionRole */
        $permissionRole = PermissionRole::findById($id);
        if (is_object($permissionRole)):
            return $permissionRole;
        endif;

        return null;
    }

    public function getAdminListRoles(): ?PermissionRoleIterator
    {
        return $this->findAll(new FindValueIterator([
                new FindValue('parentId', null),
                new FindValue('calling_name', ['$ne' => 'superadmin'])
            ]),
            false
        );
    }

    public function getAdminListChildren(string $parentid): ?PermissionRoleIterator
    {
        return $this->findAll(new FindValueIterator([
            new FindValue('parentId', $parentid),
            new FindValue('calling_name', ['$ne' => 'superadmin'])
        ]),
            false
        );
    }

    public function findAll(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null
    ): PermissionRoleIterator {
        PermissionRole::setFindPublished($hideUnpublished);
        PermissionRole::addFindOrder('name');
        if($limit !== null) :
            PermissionRole::setFindLimit($limit);
        endif;
        $this->parseFindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new PermissionRoleIterator(PermissionRole::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                PermissionRole::setFindValue(
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
                PermissionRole::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            endwhile;
        endif;
    }
}