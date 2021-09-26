<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class SensorNotFoundException extends Exception
{
    public const SENSOR_NOT_FOUND_WITH_SENSOR_NAME = 'No sensor found with the name %s';
}
