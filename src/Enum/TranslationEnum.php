<?php declare(strict_types=1);

namespace VitesseCms\User\Enum;

enum TranslationEnum
{
    case USER_LOGOUT_SUCCESS;
    case USER_LOGIN_FAILED;
    case USER_LOGIN_SUCCESS;
    case CORE_SOMETHING_IS_WRONG;
    case USER_PASSWORD_FORGOT_REQUEST_SAVED_SUCCESS;
    case USER_PASSWORD_CHANGE_SUCCESS;
}