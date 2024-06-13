<?php

namespace App\Exceptions\Common;

use Exception;

class OperatorConvertionException extends Exception
{
    public const MESSAGE = 'Operator: %s not found';
}
