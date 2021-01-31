<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Models\BlockPositionIterator;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;

class BlockPositionRepository extends \VitesseCms\Block\Repositories\BlockPositionRepository
{
    public function getByMyAccountPosition(string $role): BlockPositionIterator
    {
        return $this->findAll(
            new FindValueIterator([
                new FindValue('position', 'myaccount'),
                new FindValue('roles', ['$in' => [null, $role]])
            ]),
            true,
            null,
            new FindOrderIterator([new FindOrder('ordering',1)])
        );
    }
}