<?php declare(strict_types=1);

namespace VitesseCms\User\Repositories;

use VitesseCms\Block\Models\BlockPosition;
use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\User\Repositories\RepositoriesInterface;

class RepositoryCollection implements RepositoriesInterface
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

    public function __construct(
        UserRepository $userRepository,
        ItemRepository $itemRepository,
        PermissionRoleRepository $permissionRoleRepository,
        BlockPositionRepository $blockPositionRepository,
        BlockRepository $blockRepository
    ) {
        $this->user = $userRepository;
        $this->item = $itemRepository;
        $this->permissionRole = $permissionRoleRepository;
        $this->blockPosition = $blockPositionRepository;
        $this->block = $blockRepository;
    }
}
