<?php

namespace App\Exceptions\User\UserExceptions;

class NotAllowedToChangeUserRoleException extends \Exception
{
    public const NOT_ALLOWED_TO_CHANGE_USER_ROLE = 'You are not allowed to change the role of this user.';
}
