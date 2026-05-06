<?php

namespace App\CustomValidators\Card;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CardStateExistsConstraint extends Constraint
{
    public string $message = 'The card state with ID "{{ cardState }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
