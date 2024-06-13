<?php

namespace App\Exceptions\User;

interface UserValidationErrorInterface
{
    public function getValidationErrors(): array;
}
