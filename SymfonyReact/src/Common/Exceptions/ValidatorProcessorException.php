<?php

namespace App\Common\Exceptions;

use Exception;
use Throwable;

class ValidatorProcessorException extends Exception
{
    private array $validatorErrors;

    public const VALIDATION_ERRORS_NOT_FOUND = 'Validation errors not found';

    public function __construct(array $validatorErrors, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->validatorErrors = $validatorErrors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidatorErrors(): array
    {
        return $this->validatorErrors;
    }
}
