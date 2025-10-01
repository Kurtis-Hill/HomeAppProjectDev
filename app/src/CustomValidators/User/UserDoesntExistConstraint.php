<?php

namespace App\CustomValidators\User;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class UserDoesntExistConstraint extends Constraint
{
    public string $message = 'User "{{ user }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
