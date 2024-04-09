<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorTriggerConversionException extends Exception
{
    public const MESSAGE = 'Could not convert mixed value to string';
}
