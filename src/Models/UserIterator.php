<?php declare(strict_types=1);

namespace VitesseCms\User\Models;

use ArrayIterator;

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
