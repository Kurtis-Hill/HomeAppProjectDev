<?php

namespace App\User\Exceptions\UserExceptions;

use Exception;

class IncorrectUserPasswordException extends Exception
{
    public const MESSAGE = 'Incorrect password';
}
