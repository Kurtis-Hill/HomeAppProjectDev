<?php

namespace App\Exceptions\Device;

use Exception;

class DeviceCreationFailureException extends Exception
{
    public const DEVICE_FAILED_TO_CREATE = 'Device failed to create';
}
