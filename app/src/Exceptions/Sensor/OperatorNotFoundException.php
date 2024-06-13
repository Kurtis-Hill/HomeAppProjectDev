<?php

namespace App\Exceptions\Sensor;

use Exception;

class OperatorNotFoundException extends Exception
{
    public const MESSAGE = 'Operator with the id %d not found';
}
