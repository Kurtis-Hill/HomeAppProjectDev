<?php

namespace App\Sensors\Exceptions;

use Throwable;

class SensorRequestException extends \Exception
{
    private array $validationErrors;

    public function __construct(
        array $validationErrors,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $this->validationErrors = $validationErrors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
