<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Sht;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SHTTemperatureConstraint extends Constraint
{
    public string $minMessage = 'Temperature settings for ' . Sht::NAME . ' sensor cannot be below ' . Sht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $maxMessage = 'Temperature settings for ' . Sht::NAME . ' sensor cannot exceed ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
