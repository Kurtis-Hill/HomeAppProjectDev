<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorRequestValidators;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SensorTypeRequestConstraint extends Constraint
{
    public string $allSensorTypeFilteredMessage = 'All sensor types selected, please unselect some';
}
