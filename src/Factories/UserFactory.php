<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\User\Enums\UserRoleEnum;
use VitesseCms\User\Models\User;
use Phalcon\Di;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class UserFactory
{
    public static function create(
        string $email,
        string $password,
        string $permissionRoleId,
        bool $published = false
    ): User
    {
        $user = new User();
        $user->set('email', $email);
        $user->setPublished($published);
        if($password) :
            $user->set('password', Di::getDefault()->get('security')->hash($password));
        endif;
        $user->setRole($permissionRoleId);

        return $user;
    }

    public static function createGuest(): User
    {
        $permissionRoleRepository = new PermissionRoleRepository();
        $guestRole = $permissionRoleRepository->findFirst(new FindValueIterator(
            [new FindValue('calling_name',UserRoleEnum::GUEST)]
        ));
        $user = new User();
        if($guestRole !== null) :
            $user->setRole((string) $guestRole->getId());
        endif;

        return $user;
    }
}
