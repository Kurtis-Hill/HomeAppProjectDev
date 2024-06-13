<?php

namespace App\Exceptions\Device;

use Exception;

class DuplicateDeviceException extends Exception
{
    public const MESSAGE = 'Your group already has a device named %s that is in room %s';
}
