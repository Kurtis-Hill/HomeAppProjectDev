<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorTypeNotFoundException extends Exception
{
    public const SENSOR_TYPE_NOT_RECOGNISED = '%s sensor type not found';
}
