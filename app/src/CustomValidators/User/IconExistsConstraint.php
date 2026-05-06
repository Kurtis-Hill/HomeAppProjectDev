<?php

namespace App\CustomValidators\User;

use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class IconExistsConstraint extends Constraint
{
    public string $message = 'Icon "{{ value }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
