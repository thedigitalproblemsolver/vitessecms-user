<?php

declare(strict_types=1);

namespace VitesseCms\User\Models;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\User\Enum\SettingsEnum;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Factories\UserFactory;

class User extends AbstractCollection
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $role;
    /**
     * @var bool
     */
    public $forcePasswordReset;
    /**
     * @var null
     */
    protected $permissionRole;
    /**
     * @var bool
     */
    protected $passwordReset;
    /**
     * @var string
     */
    protected $password;

    public function afterFetch()
    {
        if ($this->name === null) {
            $this->set('name', $this->getEmail());
        }

        parent::afterFetch();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isLoggedIn(): bool
    {
        if ($this->getId()) :
            return true;
        endif;

        return false;
    }

    public function hasAdminAccess(): bool
    {
        if ($this->getId()) :
            if ($this->permissionRole === null) :
                $this->permissionRole = PermissionRole::findById($this->_('role'));
            endif;

            if (
                $this->permissionRole->_('adminAccess')
                || UserRoleEnum::isSuperAdmin($this->getPermissionRole())
            ) :
                return true;
            endif;
        endif;

        return false;
    }

    public function getPermissionRole(): string
    {
        if ($this->getId()) :
            if ($this->permissionRole === null) :
                $this->permissionRole = PermissionRole::findById($this->_('role'));
            endif;

            return $this->permissionRole->_('calling_name');
        endif;

        return UserRoleEnum::GUEST->value;
    }

    public function createLogin(string $email, string $password, string $role = null): User
    {
        if ($role === null) :
            $role = UserRoleEnum::REGISTERED->value;
        endif;
        PermissionRole::setFindValue('calling_name', $role);
        $role = PermissionRole::findFirst();

        $user = new self();
        $user->set('email', $email);
        $user->set('password', $this->di->security->hash($password));
        $user->set('role', (string)$role->getId());
        $user->set('published', true);
        $user->save();

        return $user;
    }

    public function addPersonalInformation(array $data): User
    {
        if ($this->getDI()->get('setting')->has(SettingsEnum::USER_DATAGROUP_PERSONALINFORMATION->name)) :
            /** @var Datagroup $datagroup */
            $datagroup = Datagroup::findById(
                $this->getDI()->get('setting')->getString(SettingsEnum::USER_DATAGROUP_PERSONALINFORMATION->name)
            );
            if ($datagroup) :
                UserFactory::bindByDatagroup($datagroup, $data, $this, new DatafieldRepository());
            endif;
        endif;

        return $this;
    }

    public function hasForcedPasswordReset(): bool
    {
        return (bool)$this->forcePasswordReset;
    }

    public function setForcePasswordReset(bool $forcePasswordReset): User
    {
        $this->forcePasswordReset = $forcePasswordReset;

        return $this;
    }

    public function setPasswordReset(bool $passwordReset): User
    {
        $this->passwordReset = $passwordReset;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): User
    {
        $this->role = $role;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }
}
