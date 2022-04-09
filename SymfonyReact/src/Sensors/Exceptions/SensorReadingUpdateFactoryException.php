<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorReadingUpdateFactoryException extends Exception
{
    public const MESSAGE = 'Sensor reading update factory error, type not recognized';
}
