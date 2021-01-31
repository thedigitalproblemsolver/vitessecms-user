<?php declare(strict_types=1);

namespace VitesseCms\User\Models;

use VitesseCms\Database\AbstractCollection;

class PermissionRole extends AbstractCollection
{
    /**
     * @var string
     */
    public $calling_name;

    /**
     * @var bool
     */
    public $adminAccess;

    public function setCallingName(string $calling_name): PermissionRole
    {
        $this->calling_name = $calling_name;

        return $this;
    }

    public function setAdminAccess(bool $adminAccess): PermissionRole
    {
        $this->adminAccess = $adminAccess;

        return $this;
    }
}
