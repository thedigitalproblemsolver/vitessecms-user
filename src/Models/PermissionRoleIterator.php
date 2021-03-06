<?php declare(strict_types=1);

namespace VitesseCms\User\Models;

use ArrayIterator;
use VitesseCms\Database\AbstractCollection;

class PermissionRoleIterator extends ArrayIterator
{
    public function __construct(array $permissionRoles)
    {
        parent::__construct($permissionRoles);
    }

    public function current(): PermissionRole
    {
        return parent::current();
    }
}