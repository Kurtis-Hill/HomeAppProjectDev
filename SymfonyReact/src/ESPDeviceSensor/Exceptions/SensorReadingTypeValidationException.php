<?php

namespace App\ESPDeviceSensor\Exceptions;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use JetBrains\PhpStorm\Pure;

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
