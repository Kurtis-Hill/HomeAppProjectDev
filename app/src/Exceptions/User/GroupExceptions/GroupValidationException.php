<?php

namespace App\Exceptions\User\GroupExceptions;

use App\Exceptions\User\UserValidationErrorInterface;
use Exception;
use Throwable;

class GroupValidationException extends Exception implements UserValidationErrorInterface
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
