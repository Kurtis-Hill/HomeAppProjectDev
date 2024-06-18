<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorDataCurrentReadingUpdateBuilderException extends Exception
{
    public const NOT_ARRAY_ERROR_MESSAGE = 'Sensor data current reading update error wrong type supplied expected array';
}
