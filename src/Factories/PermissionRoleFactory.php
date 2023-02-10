<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use VitesseCms\User\Models\PermissionRole;

class PermissionRoleFactory
{
    public static function create(
        string $name,
        string $calling_name,
        bool   $published = false,
        bool   $adminAccess = false,
        string $parentId = null
    ): PermissionRole
    {
        $permission = new PermissionRole();
        $permission->set('name', $name, true);
        $permission->setCallingName($calling_name);
        $permission->setPublished($published);
        $permission->setAdminAccess($adminAccess);
        $permission->setParent($parentId);

        return $permission;
    }
}
