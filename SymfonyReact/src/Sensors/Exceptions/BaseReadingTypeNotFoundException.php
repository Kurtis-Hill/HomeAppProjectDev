<?php

namespace App\Sensors\Exceptions;

use Exception;

class BaseReadingTypeNotFoundException extends Exception
{
    public const MESSAGE = 'Base reading type with the id %d not found';
}
