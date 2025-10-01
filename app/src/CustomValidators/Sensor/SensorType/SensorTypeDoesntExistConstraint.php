<?php

namespace App\CustomValidators\Sensor\SensorType;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SensorTypeDoesntExistConstraint extends Constraint
{
    public string $message = 'Sensor type "{{ sensorType }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
