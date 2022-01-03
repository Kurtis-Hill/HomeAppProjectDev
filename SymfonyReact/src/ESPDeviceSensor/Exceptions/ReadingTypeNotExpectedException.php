<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class ReadingTypeNotExpectedException extends Exception
{
    public const READING_TYPE_NOT_EXPECTED = 'Reading type not expected';
}