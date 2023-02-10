<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use Phalcon\Di\Di;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class UserFactory
{
    public static function create(
        string $email,
        string $password,
        string $permissionRoleId,
        bool   $published = false
    ): User
    {
        $user = new User();
        $user->set('email', $email);
        $user->setPublished($published);
        if ($password) :
            $user->set('password', Di::getDefault()->get('security')->hash($password));
        endif;
        $user->setRole($permissionRoleId);

        return $user;
    }

    public static function createGuest(): User
    {
        $permissionRoleRepository = new PermissionRoleRepository();
        $guestRole = $permissionRoleRepository->findFirst(new FindValueIterator(
            [new FindValue('calling_name', UserRoleEnum::GUEST->value)]
        ));
        $user = new User();
        if ($guestRole !== null) :
            $user->setRole((string)$guestRole->getId());
        endif;

        return $user;
    }

    public static function bindByDatagroup(Datagroup $datagroup, array $data, User $user, DatafieldRepository $datafieldRepository)
    {
        foreach ($datagroup->getDatafields() as $field) :
            $datafield = $datafieldRepository->getById($field['id']);
            if ($datafield !== null) :
                if (isset($data[$datafield->getCallingName()])):
                    $user->set($datafield->getCallingName(), $data[$datafield->getCallingName()]);
                endif;

                if (isset($data['BSON_' . $datafield->getCallingName()])) :
                    $user->set('BSON_' . $datafield->getCallingName(), $data['BSON_' . $datafield->getCallingName()]);
                endif;
            endif;
        endforeach;
    }
}
