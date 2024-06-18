<?php

namespace App\Exceptions\Sensor;

use Exception;
use Throwable;

class UpdateCurrentReadingValidationErrorException extends Exception
{
    public function __construct(private readonly array $validationErrors = [], string $message = "",int $code = 0,?Throwable $previous = null) {parent::__construct($message,$code,$previous);}

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
