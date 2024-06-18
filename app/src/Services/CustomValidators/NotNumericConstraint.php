<?php

namespace App\Services\CustomValidators;

use Symfony\Component\Validator\Constraint;

class NotNumericConstraint extends Constraint
{
    public string $message = "The name cannot contain any numbers, please choose a different name";
}
