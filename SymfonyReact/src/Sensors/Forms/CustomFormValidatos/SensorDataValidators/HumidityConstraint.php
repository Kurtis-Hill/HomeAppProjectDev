<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use App\Sensors\Entity\ReadingTypes\Humidity;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class HumidityConstraint extends Constraint
{
    public string $minMessage = 'Humidity for this sensor cannot be under 0 you entered {{ string }}'. Humidity::READING_SYMBOL;

    public string $maxMessage = 'Humidity for this sensor cannot be over 100 you entered {{ string }}'. Humidity::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
