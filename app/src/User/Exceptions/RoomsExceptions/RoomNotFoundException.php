<?php

namespace App\User\Exceptions\RoomsExceptions;

use Exception;

class RoomNotFoundException extends Exception
{
    public const MESSAGE = 'Room not found';

    public const MESSAGE_WITH_ID = 'Room not found for id %d';
}
