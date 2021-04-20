<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Models\BlockPosition;
use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
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

    /**
     * @var DatafieldRepository
     */
    public $datafield;

    public function __construct(
        UserRepository $userRepository,
        ItemRepository $itemRepository,
        PermissionRoleRepository $permissionRoleRepository,
        BlockPositionRepository $blockPositionRepository,
        BlockRepository $blockRepository,
        DatagroupRepository $datagroupRepository,
        DatafieldRepository $datafieldRepository
    )
    {
        $this->user = $userRepository;
        $this->item = $itemRepository;
        $this->permissionRole = $permissionRoleRepository;
        $this->blockPosition = $blockPositionRepository;
        $this->block = $blockRepository;
        $this->datagroup = $datagroupRepository;
        $this->datafield = $datafieldRepository;
    }
}
