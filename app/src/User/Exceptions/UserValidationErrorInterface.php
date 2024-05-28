<?php

namespace App\User\Exceptions;

interface UserValidationErrorInterface
{
    public function getValidationErrors(): array;
}
