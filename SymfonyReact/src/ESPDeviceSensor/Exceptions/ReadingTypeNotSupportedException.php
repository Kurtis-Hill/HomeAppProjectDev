<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class ReadingTypeNotSupportedException extends Exception
{
    public const READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE = "Reading type is not supported for sensor name: %s";
}
