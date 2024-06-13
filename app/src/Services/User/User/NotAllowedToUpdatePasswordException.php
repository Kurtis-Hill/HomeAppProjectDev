<?php

namespace App\Services\User\User;

use Exception;

class NotAllowedToUpdatePasswordException extends Exception
{
    public const NOT_ALLOWED_TO_UPDATE_PASSWORD = 'You are not allowed to update the password of this user.';
}
