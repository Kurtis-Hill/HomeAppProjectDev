<?php

namespace App\Exceptions\Sensor;

use Exception;

class ReadingTypeNotGivenException extends Exception
{
    public const MESSAGE = 'Reading type not given';
}
