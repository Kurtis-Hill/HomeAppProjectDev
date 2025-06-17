<?php

namespace App\CustomValidators\User;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class ColourExistsConstraint extends Constraint
{
    public string $message = 'Colour "{{ value }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
