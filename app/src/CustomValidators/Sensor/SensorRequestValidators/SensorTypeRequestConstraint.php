<?php

namespace App\CustomValidators\Sensor\SensorRequestValidators;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SensorTypeRequestConstraint extends Constraint
{
    public string $allSensorTypeFilteredMessage = 'All sensor types selected, please unselect some';
}
