<?php

namespace App\UserInterface\Exceptions;

use Exception;

class FailedToFilterSensorTypesException extends Exception
{
    public const FAILED_TO_FILTER_SENSOR_TYPES = 'Failed to filter sensor types';
}
