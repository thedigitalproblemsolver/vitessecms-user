<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Models;

use VitesseCms\User\Repositories\PermissionRoleRepository;

class PermissionRoleListener
{
    public function __construct(private readonly PermissionRoleRepository $permissionRoleRepository)
    {
    }

    public function getRepository(): PermissionRoleRepository
    {
        return $this->permissionRoleRepository;
    }
}