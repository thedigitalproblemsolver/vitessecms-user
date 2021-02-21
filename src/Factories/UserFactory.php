<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use VitesseCms\User\Enums\UserRoleEnum;
use VitesseCms\User\Models\PermissionRole;
use VitesseCms\User\Models\User;
use Phalcon\Di;

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
        //if($password) :
        //$user->set('password', Di::getDefault()->get('security')->hash($password));
        //endif;

        PermissionRole::setFindValue('calling_name', $calling_name);
        $user->setRole((string)PermissionRole::findFirst()->getId());

        /*if( is_object($bindData)) :
            Datagroup::setFindPublished(false);
            $datagroup = Datagroup::findById($datagroupId);
            foreach ($datagroup->_('datafields') as $datafieldId => $groupDatafield ) :
                Datafield::setFindPublished(false);
                $datafield = Datafield::findById($datafieldId);

    var_dump('UserFactory '.$datafield->_('calling_name'));
            die();
            endforeach;
        endif;*/

        return $user;
    }

    public static function createGuest(): User
    {
        $user = new User();
        $user->setRole(UserRoleEnum::GUEST);

        return $user;
    }
}
