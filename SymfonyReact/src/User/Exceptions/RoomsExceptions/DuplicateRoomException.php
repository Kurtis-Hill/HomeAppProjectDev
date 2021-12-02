<?php

namespace App\User\Exceptions\RoomsExceptions;

use Exception;

class DuplicateRoomException extends Exception
{
    public const MESSAGE = 'A sensor with the name: %s already exists';
}
