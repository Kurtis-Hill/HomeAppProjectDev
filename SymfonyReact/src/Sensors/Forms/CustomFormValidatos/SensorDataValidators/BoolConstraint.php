<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class BoolConstraint extends Constraint
{
    public string $message = 'Bool readings can only be true or false';
}
