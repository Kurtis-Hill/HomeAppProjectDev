<?php

namespace App\User\Exceptions\RoomsExceptions;

use Exception;

class RoomNotFoundException extends Exception
{
    public const MESSAGE = 'Room not found';
}
