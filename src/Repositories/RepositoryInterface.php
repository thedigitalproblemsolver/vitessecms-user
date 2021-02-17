<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

/**
 * @property ItemRepository $item
 * @property UserRepository $user
 * @property PermissionRoleRepository $permissionRole
 * @property BlockPositionRepository $blockPosition
 * @property BlockRepository $block
 * @property DatagroupRepository $datagroup
 */
interface RepositoryInterface
{
}
