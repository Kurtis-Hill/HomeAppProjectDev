<?php

namespace App\Common\Exceptions;

use Exception;

class ValidatorProcessorException extends Exception
{
    public const VALIDATION_ERRORS_NOT_FOUND = 'Validation errors not found';
}
