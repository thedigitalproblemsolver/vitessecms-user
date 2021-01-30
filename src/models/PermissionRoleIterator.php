<?php declare(strict_types=1);

namespace VitesseCms\User\Models;

use VitesseCms\Database\AbstractCollection;

class PermissionRoleIterator extends \ArrayIterator
{
    public function __construct(array $products)
    {
        parent::__construct($products);
    }

    public function current(): PermissionRole
    {
        return parent::current();
    }

    public function add(PermissionRole $value): void
    {
        $this->append($value);
    }
}