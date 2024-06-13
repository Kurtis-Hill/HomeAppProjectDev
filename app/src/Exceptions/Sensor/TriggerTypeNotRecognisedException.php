<?php

namespace App\Exceptions\Sensor;

use Exception;

class TriggerTypeNotRecognisedException extends Exception
{
    public const MESSAGE = '%s Trigger type not recognised';
}
