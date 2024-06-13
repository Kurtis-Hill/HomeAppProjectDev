<?php

namespace App\Exceptions\Device;

use Exception;

class DeviceQueryException extends Exception
{
    public const FAILED_TO_QUERY_DEVICES = 'Failed to query devices';
}
