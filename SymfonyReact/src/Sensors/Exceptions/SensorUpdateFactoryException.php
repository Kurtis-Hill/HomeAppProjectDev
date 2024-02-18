<?php

namespace App\Sensors\Exceptions;

use Exception;

class SensorUpdateFactoryException extends Exception
{
    public const SENSOR_BUILDER_NOT_FOUND = 'Sensor update builder not found';

    public const SENSOR_BUILDER_NOT_FOUND_SPECIFIC = 'Sensor update builder not found: %s';
}
