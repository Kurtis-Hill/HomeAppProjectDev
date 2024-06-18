<?php

namespace App\Exceptions\User\UserExceptions;

use Exception;

class IncorrectUserPasswordException extends Exception
{
    public const MESSAGE = 'Incorrect password';
}
