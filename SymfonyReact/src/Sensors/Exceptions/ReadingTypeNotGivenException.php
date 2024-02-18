<?php

namespace App\Sensors\Exceptions;

use Exception;

class ReadingTypeNotGivenException extends Exception
{
    public const MESSAGE = 'Reading type not given';
}
