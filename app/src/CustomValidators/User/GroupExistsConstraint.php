<?php

namespace App\CustomValidators\User;

use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class GroupExistsConstraint extends Constraint
{
    public string $message = GroupNotFoundException::MESSAGE_CONSTRAINT .'{{ group }}';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
