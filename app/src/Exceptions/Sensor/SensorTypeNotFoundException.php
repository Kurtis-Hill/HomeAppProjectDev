<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorTypeNotFoundException extends Exception
{
    public const SENSOR_TYPE_NOT_RECOGNISED = '%s sensor type not found';

    public const SENSOR_TYPE_NOT_FOUND_FOR_ID = 'Sensor type not found for id %d';
}
