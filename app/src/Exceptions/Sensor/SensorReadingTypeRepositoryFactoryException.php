<?php

namespace App\Exceptions\Sensor;

use Exception;

class SensorReadingTypeRepositoryFactoryException extends Exception
{
    public const READING_TYPE_NOT_FOUND = '%s Sensor type not recognised';
}
