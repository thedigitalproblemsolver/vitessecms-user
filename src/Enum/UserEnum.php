<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum UserEnum: string
{
    case ON_LOGIN_SUCCESS_LISTENER = 'user:onLoginSuccess';
    case SERVICE_LISTENER = 'UserListener';
    case GET_ACTIVE_USER_LISTENER = 'UserListener:getActiveUser';
    case GET_REPOSITORY = 'UserListener:getRepository';

}