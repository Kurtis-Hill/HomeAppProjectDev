<?php

namespace App\Services\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Sht;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SHTTemperatureConstraint extends Constraint
{
    public string $minMessage = 'Temperature settings for ' . Sht::NAME . ' sensor cannot be below ' . Sht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $maxMessage = 'Temperature settings for ' . Sht::NAME . ' sensor cannot exceed ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
