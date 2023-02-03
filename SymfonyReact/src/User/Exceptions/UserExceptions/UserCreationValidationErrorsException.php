<?php

namespace App\User\Exceptions\UserExceptions;

use Exception;

class UserCreationValidationErrorsException extends Exception
{
    private array $validationErrors;

    public function __construct(array $errors, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->validationErrors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
