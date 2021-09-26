<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class SensorTypeException extends Exception
{
    public const SENSOR_TYPE_NOT_RECOGNISED = 'Sensor Type Not Recognised Your App May Need Updating: %s';
}
