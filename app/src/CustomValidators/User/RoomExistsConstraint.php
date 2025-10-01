<?php

namespace App\CustomValidators\User;

use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class RoomExistsConstraint extends Constraint
{
    public string $message = RoomNotFoundException::MESSAGE_WITH_ID_CONSTRAINT . ' {{ room }}';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
