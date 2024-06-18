<?php

namespace App\Exceptions\Sensor;

use Exception;

class ReadingTypeNotExpectedException extends Exception
{
    public const READING_TYPE_NOT_EXPECTED = 'Reading type not expected got %s expected %s';
    public const READING_TYPE_NOT_EXPECTED_PLAIN = 'Reading type not expected';
}
