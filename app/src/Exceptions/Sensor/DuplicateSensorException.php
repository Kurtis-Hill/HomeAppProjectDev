<?php

namespace App\Exceptions\Sensor;

use Exception;

class DuplicateSensorException extends Exception
{
    public const MESSAGE = 'Sensor with name %s already exists';

    public const MESSAGE_GROUP = 'Sensor with name %s already exists in group %s';

    public const MESSAGE_PIN = 'Sensor with pin %d already exists';
}
