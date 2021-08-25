<?php

namespace App\Exceptions;

use Exception;

class DeviceNotFoundException extends Exception
{
    public const DEVICE_NOT_FOUND_MESSAGE_WITH_SENSOR_NAME_AND_DEVICE_ID = 'Device does not have a sensor with the name %s and id %u';
}
