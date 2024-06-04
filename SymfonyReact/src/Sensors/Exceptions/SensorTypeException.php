<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorTypeException extends Exception
{
    public const SENSOR_TYPE_NOT_RECOGNISED = 'Sensor Type: %s Not Recognised Your App May Need Updating';

    public const SENSOR_TYPE_NOT_RECOGNISED_NO_NAME = 'Sensor Type Not Recognised Your App May Need Updating';

    public const SENSOR_TYPE_NOT_ALLOWED = 'SensorType %s Not Allowed';
}
