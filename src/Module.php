<?php declare(strict_types=1);

namespace VitesseCms\User;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Repositories\BlockPositionRepository;
use VitesseCms\User\Repositories\PermissionRoleRepository;
use VitesseCms\User\Repositories\RepositoryCollection;
use VitesseCms\User\Repositories\UserRepository;
use Phalcon\Di\DiInterface;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'User');
        $di->setShared('repositories', new RepositoryCollection(
            new UserRepository(),
            new ItemRepository(),
            new PermissionRoleRepository(),
            new BlockPositionRepository(),
            new BlockRepository(),
            new DatagroupRepository(),
            new DatafieldRepository()
        ));
    }
}
