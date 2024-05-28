<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorReadingTypeObjectNotFoundException extends Exception
{
    public const SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION = 'Sensor reading type object not found';
}
