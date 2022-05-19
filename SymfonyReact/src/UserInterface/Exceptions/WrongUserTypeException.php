<?php

namespace App\UserInterface\Exceptions;

use Exception;

class WrongUserTypeException extends Exception
{
    public const WRONG_USER_TYPE_MESSAGE = 'Wrong user type';
}
