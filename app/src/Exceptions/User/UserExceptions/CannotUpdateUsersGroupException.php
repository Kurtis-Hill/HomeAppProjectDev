<?php

namespace App\Exceptions\User\UserExceptions;

use Exception;

class CannotUpdateUsersGroupException extends Exception
{
    public const CANNOT_UPDATE_USERS_GROUP = 'You are not allowed to change the group of this user.';
}
