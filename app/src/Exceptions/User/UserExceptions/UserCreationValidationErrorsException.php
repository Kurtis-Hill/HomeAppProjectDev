<?php

namespace App\Exceptions\User\UserExceptions;

use App\Exceptions\User\UserValidationErrorInterface;
use Exception;
use Throwable;

class UserCreationValidationErrorsException extends Exception implements UserValidationErrorInterface
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
