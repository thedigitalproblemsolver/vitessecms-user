<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners;

use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\UserRepository;

class UserListener
{
    private User $activeUser;
    private UserRepository $userRepository;

    public function __construct(User $activeUser, UserRepository $userRepository)
    {
        $this->activeUser = $activeUser;
        $this->userRepository = $userRepository;
    }

    public function getActiveUser(): User
    {
        return $this->activeUser;
    }

    public function getRepository(): UserRepository
    {
        return $this->userRepository;
    }
}
