<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Models\BlockPosition;
use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\Repositories\DatagroupRepository;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\User\Repositories\RepositoriesInterface;

class RepositoryCollection implements RepositoriesInterface, BaseRepositoriesInterface
{
    /**
     * @var UserRepository
     */
    public $user;

    /**
     * @var ItemRepository
     */
    public $item;

    /**
     * @var PermissionRoleRepository
     */
    public $permissionRole;

    /**
     * @var BlockPositionRepository
     */
    public $blockPosition;

    /**
     * @var BlockRepository
     */
    public $block;

    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    public function __construct(
        UserRepository $userRepository,
        ItemRepository $itemRepository,
        PermissionRoleRepository $permissionRoleRepository,
        BlockPositionRepository $blockPositionRepository,
        BlockRepository $blockRepository,
        DatagroupRepository $datagroupRepository
    ) {
        $this->user = $userRepository;
        $this->item = $itemRepository;
        $this->permissionRole = $permissionRoleRepository;
        $this->blockPosition = $blockPositionRepository;
        $this->block = $blockRepository;
        $this->datagroup = $datagroupRepository;
    }
}
