<?php

namespace App\Common\Exceptions;

use Exception;

class OperatorConvertionException extends Exception
{
    public const MESSAGE = 'Operator: %s not found';
}
