<?php

namespace App\Services\CustomValidators\Device;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DuplicateDeviceCheckConstraint extends Constraint
{
    public string $message = 'Your group already has a device named {{ value }} that is in room {{ room }}.';
}
