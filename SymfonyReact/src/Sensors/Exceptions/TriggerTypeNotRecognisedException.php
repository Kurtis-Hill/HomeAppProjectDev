<?php

namespace App\Sensors\Exceptions;

use Exception;

class TriggerTypeNotRecognisedException extends Exception
{
    public const MESSAGE = '%s TriggerControllers type not recognised';
}
