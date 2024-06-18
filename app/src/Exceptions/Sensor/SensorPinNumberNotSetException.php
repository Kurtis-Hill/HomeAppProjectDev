<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorPinNumberNotSetException extends Exception
{
    public const MESSAGE = 'Sensor ID %d pin number not set';
}
