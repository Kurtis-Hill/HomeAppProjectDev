<?php

namespace App\Sensors\Exceptions;

use Exception;

class ReadingTypeNotSupportedException extends Exception
{
    public const READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE = "Reading type is not supported for sensor name: %s";

    public const READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE = "Reading type is not supported. Please update the app.";

    public const READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR = '%s reading type is not supported for sensor name: %s';
}
