<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

/**
 * @property ItemRepository $item
 * @property UserRepository $user
 * @property PermissionRoleRepository $permissionRole
 * @property BlockRepository $block
 * @property DatagroupRepository $datagroup
 * @property DatafieldRepository $datafield
 */
interface RepositoryInterface
{
}
