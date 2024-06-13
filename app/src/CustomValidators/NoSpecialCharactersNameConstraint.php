<?php

namespace App\CustomValidators;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class NoSpecialCharactersNameConstraint extends Constraint
{
    public string $message = "The name cannot contain any special characters, please choose a different name";
}
