<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorTypeObjectBuilderException extends Exception
{
    public const MESSAGE = 'Sensor type object builder type not recognised app may need updating';
}
