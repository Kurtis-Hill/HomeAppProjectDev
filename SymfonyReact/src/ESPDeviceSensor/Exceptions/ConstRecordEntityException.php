<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;

class ConstRecordEntityException extends Exception
{
    public const CONST_RECORD_ENTITY_NOT_FOUND_MESSAGE = 'Constant Record entity is not supported here, check if your app is up to date sensorID: %s';
}
