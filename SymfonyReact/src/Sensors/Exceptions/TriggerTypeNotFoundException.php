<?php

namespace App\Sensors\Exceptions;

use Exception;

class TriggerTypeNotFoundException extends Exception
{
    public const MESSAGE = 'Trigger type with the id %d not found';
}
