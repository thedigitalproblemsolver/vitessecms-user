<?php

declare(strict_types=1);

namespace VitesseCms\User\Services;

use VitesseCms\Core\Services\RouterService;
use VitesseCms\User\Models\User;
use VitesseCms\User\Utils\PermissionUtils;

class AclService
{
    private User $activeUser;
    private RouterService $routerService;

    public function __construct(User $user, RouterService $routerService)
    {
        $this->activeUser = $user;
        $this->routerService = $routerService;
    }

    public function hasAccess(string $function): bool
    {
        return PermissionUtils::check(
            $this->activeUser,
            $this->routerService->getModulePrefix().$this->routerService->getModuleName(),
            $this->routerService->getControllerName(),
            $function
        );
    }
}
