<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class ReadingTypeObjectBuilderException extends Exception
{
    public const OBJECT_NOT_FOUND_MESSAGE = '%s object not of the correct type for this builder';
}
