<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorTriggerConversionException extends Exception
{
    public const MESSAGE = 'Could not convert mixed value to string';
}
