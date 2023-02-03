<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

use VitesseCms\Core\AbstractEnum;

class UserEnum extends AbstractEnum
{
    public const ON_LOGIN_SUCCESS_LISTENER = 'user:onLoginSuccess';
    public const SERVICE_LISTENER = 'UserListener';
    public const GET_ACTIVE_USER_LISTENER = 'UserListener:getActiveUser';
    public const GET_REPOSITORY = 'UserListener:getRepository';

}