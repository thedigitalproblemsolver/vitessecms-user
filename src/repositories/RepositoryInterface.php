<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;

/**
 * @property ItemRepository $item
 * @property UserRepository $user
 * @property PermissionRoleRepository $permissionRole
 * @property BlockPositionRepository $blockPosition
 * @property BlockRepository $block
 */
interface RepositoryInterface
{
}
