<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorReadingUpdateFactoryException extends Exception
{
    public const MESSAGE = 'Sensor reading update factory error, %s not recognized';
}
