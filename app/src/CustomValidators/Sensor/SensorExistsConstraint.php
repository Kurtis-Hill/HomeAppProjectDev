<?php

namespace App\CustomValidators\Sensor;

use App\Exceptions\Sensor\SensorNotFoundException;
use Symfony\Component\Validator\Constraint;

class SensorExistsConstraint extends Constraint
{
    public string $message = SensorNotFoundException::SENSOR_NOT_FOUND_WITH_SENSOR_NAME . '{{ sensorID }}';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
