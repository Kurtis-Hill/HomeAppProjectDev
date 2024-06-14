<?php

namespace App\Services\CustomValidators\Sensor\SensorRequestValidators;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ReadingTypeRequestConstraint extends Constraint
{
    public string $allReadingTypeFilteredMessage = 'All reading types selected, please unselect some';
}
