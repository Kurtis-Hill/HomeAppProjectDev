<?php

namespace App\Exceptions;

use Exception;

class ConstRecordEntityException extends Exception
{
    public const OUT_OF_BOUNDS_ENTITY_NOT_FOUND_MESSAGE = 'Constant Record entity is not supported here, check if your app is up to date sensorID: %s';
}
