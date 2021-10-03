<?php


namespace App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators;

use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use Symfony\Component\Validator\Constraint;

class DallasTemperatureConstraint extends Constraint
{
    public string $minMessage = 'Temperature settings for Dallas sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $maxMessage = 'Temperature settings for Dallas sensor cannot exceed ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
