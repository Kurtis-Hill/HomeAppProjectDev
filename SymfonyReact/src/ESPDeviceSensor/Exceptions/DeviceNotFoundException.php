<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class DeviceNotFoundException extends Exception
{
    public const DEVICE_NOT_FOUND_MESSAGE_WITH_SENSOR_NAME_AND_DEVICE_ID = 'Device does not have a sensor with the name %s and id %u';

    public const DEVICE_SENSOR_AND_TYPE_NOT_MATCHED = '%s device type does not match the type supplied %s';
}
