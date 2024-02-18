<?php

namespace App\Sensors\Exceptions;

use Exception;

class UserNotAllowedException extends Exception
{
    public const MESSAGE = '%s UserExceptions is not allowed to create a new sensor';
}
