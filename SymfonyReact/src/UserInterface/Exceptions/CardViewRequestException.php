<?php

namespace App\UserInterface\Exceptions;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Throwable;

class CardViewRequestException extends Exception
{
    private array $validationErrors = [];

    public function __construct(string|array $message = "", int $code = 0, ?Throwable $previous = null)
    {

        if (is_array($message)) {
            $this->validationErrors = $message;
            $message = implode("\n", $message);
        }

        parent::__construct($message, $code, $previous);
    }

    #[ArrayShape(['validationErrors'])]
    public function getValidationErrorsArray(): array
    {
        return $this->validationErrors;
    }
}
