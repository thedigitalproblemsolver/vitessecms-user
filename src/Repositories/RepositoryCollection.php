<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

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
        UserRepository           $userRepository,
        ItemRepository           $itemRepository,
        PermissionRoleRepository $permissionRoleRepository,
        BlockRepository          $blockRepository,
        DatagroupRepository      $datagroupRepository,
        DatafieldRepository      $datafieldRepository
    )
    {
        $this->user = $userRepository;
        $this->item = $itemRepository;
        $this->permissionRole = $permissionRoleRepository;
        $this->block = $blockRepository;
        $this->datagroup = $datagroupRepository;
        $this->datafield = $datafieldRepository;
    }
}
