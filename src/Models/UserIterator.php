<?php declare(strict_types=1);

namespace VitesseCms\User\Models;

use ArrayIterator;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Core\Models\Datagroup;

class UserIterator extends ArrayIterator
{
    public function __construct(array $users)
    {
        parent::__construct($users);
    }

    public function current(): User
    {
        return parent::current();
    }
}
