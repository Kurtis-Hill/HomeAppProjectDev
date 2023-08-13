<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorPinNumberNotSetException extends Exception
{
    public const MESSAGE = 'Sensor ID %d pin number not set';
}
