<?php

namespace App\Sensors\Exceptions;

use Exception;

class UserNotAllowedException extends Exception
{
    public const MESSAGE = '%s User is not allowed to create a new sensor';
}
