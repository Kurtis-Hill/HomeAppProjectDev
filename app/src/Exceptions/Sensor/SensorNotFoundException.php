<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorNotFoundException extends Exception
{
    public const SENSOR_NOT_FOUND_WITH_SENSOR_NAME = 'No sensor found with the name %s';

    public const SENSOR_NOT_FOUND_WITH_SENSOR_NAME_CONSTRAINT = 'No sensor found with the name ';
}
