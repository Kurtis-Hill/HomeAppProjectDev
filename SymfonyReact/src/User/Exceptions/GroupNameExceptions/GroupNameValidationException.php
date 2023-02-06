<?php

namespace App\User\Exceptions\GroupNameExceptions;

use App\User\Exceptions\UserValidationErrorInterface;
use Exception;
use Throwable;

class GroupNameValidationException extends Exception implements UserValidationErrorInterface
{
    private array $validationErrors;

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
