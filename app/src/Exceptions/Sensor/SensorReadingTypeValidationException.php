<?php

namespace App\Exceptions\Sensor;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class SensorReadingTypeValidationException extends Exception
{
    private array $validationErrors;

    #[Pure]
    public function __construct(array $validationErrors, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->validationErrors = $validationErrors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
