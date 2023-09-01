<?php

namespace App\Devices\Exceptions;

use Exception;

class DeviceQueryException extends Exception
{
    public const FAILED_TO_QUERY_DEVICES = 'Failed to query devices';
}
