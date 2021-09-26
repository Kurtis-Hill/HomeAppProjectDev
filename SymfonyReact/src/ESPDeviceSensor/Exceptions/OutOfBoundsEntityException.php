<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class OutOfBoundsEntityException extends Exception
{
    public const OUT_OF_BOUNDS_ENTITY_NOT_FOUND_MESSAGE = 'Out of bounds entity is not supported here, check if your app is up to date sensorID: %s';
}
