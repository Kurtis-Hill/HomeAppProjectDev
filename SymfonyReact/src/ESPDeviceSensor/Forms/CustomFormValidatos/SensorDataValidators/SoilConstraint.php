<?php

namespace App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SoilConstraint extends Constraint
{
    public string $maxMessage = 'Reading for this sensor cannot be over 9999 you entered {{ string }}';

    public string $minMessage = 'Reading for this sensor cannot be under 1000 you entered {{ string }}';

    public string $intMessage = 'The submitted value is not a number {{ string }}';
}
