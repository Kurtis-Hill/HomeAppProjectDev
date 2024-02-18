<?php

namespace App\Sensors\Exceptions;

use Exception;

class DuplicateSensorException extends Exception
{
    public const MESSAGE = 'Sensor with name %s already exists';
}
