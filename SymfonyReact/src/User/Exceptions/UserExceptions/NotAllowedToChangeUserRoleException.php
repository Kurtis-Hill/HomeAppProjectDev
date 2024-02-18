<?php

namespace App\User\Exceptions\UserExceptions;

use Doctrine\DBAL\Exception;

class NotAllowedToChangeUserRoleException extends Exception
{
    public const NOT_ALLOWED_TO_CHANGE_USER_ROLE = 'You are not allowed to change the role of this user.';
}
