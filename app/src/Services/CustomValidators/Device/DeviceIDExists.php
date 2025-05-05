<?php

namespace App\Services\CustomValidators\Device;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DeviceIDExists extends Constraint
{
    public string $message = 'The device with ID "{{ deviceID }}" does not exist.';

//    public function getTargets(): string
//    {
//        return self::CLASS_CONSTRAINT;
//    }
}
