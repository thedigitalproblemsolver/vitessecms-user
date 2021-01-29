<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use VitesseCms\User\Models\PermissionRole;

class PermissionRoleFactory
{
    public static function create(
        string $name,
        string $calling_name,
        bool $published = false,
        bool $adminAccess = false,
        string $parentId = null
    ) : PermissionRole
    {
        $permission = new PermissionRole();
        $permission->set('name', $name,true);
        $permission->set('calling_name', $calling_name);
        $permission->set('published', $published);
        $permission->set('adminAccess', $adminAccess);
        $permission->set('parentId', $parentId);

        return $permission;
    }
}
