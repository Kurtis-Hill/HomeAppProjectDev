<?php

namespace App\Services\CustomValidators\Sensor;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class UniqueSensorForDevice extends Constraint
{
    public string $message = 'A sensor with the name "{{ sensorName }}" already exists for the device with ID "{{ deviceID }}".';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
