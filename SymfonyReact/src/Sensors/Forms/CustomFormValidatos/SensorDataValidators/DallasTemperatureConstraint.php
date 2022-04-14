<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Dallas;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DallasTemperatureConstraint extends Constraint
{
    public string $minMessage = 'Temperature settings for Dallas sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $maxMessage = 'Temperature settings for Dallas sensor cannot exceed ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
