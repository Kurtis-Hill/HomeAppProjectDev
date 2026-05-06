<?php

namespace App\CustomValidators\Authenication;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class GroupMappingExistsConstraint extends Constraint
{
    public string $message = 'The group mapping with ID "{{ groupMapping }}" does not exist';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
