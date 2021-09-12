<?php

namespace App\Exceptions;

use Exception;

class ReadingTypeNotSupportedException extends Exception
{
    public const READEING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE = "Reading type is not supported for sensor name: %s";
}
