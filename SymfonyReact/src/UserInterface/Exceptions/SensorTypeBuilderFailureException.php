<?php

namespace App\UserInterface\Exceptions;

use Exception;

class SensorTypeBuilderFailureException extends Exception
{
    public const SENSOR_TYPE_BUILDER_FAILURE_MESSAGE = 'Sensor type builder failure for sensor %s';
}
