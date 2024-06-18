<?php

namespace App\Exceptions\Sensor;

use Exception;

class ReadingTypeObjectBuilderException extends Exception
{
    public const OBJECT_NOT_FOUND_MESSAGE = '%s object not of the correct type for this builder';

    public const CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE = 'Failed to build current reading for type %s';
}
