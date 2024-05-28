<?php

namespace App\User\Exceptions\RoomsExceptions;

use Exception;

class DuplicateRoomException extends Exception
{
    public const MESSAGE = 'A room with the name: %s already exists in this group';
}
