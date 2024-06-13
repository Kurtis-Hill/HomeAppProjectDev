<?php

namespace App\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Bmp;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BMP280TemperatureConstraint extends Constraint
{
    public string $maxMessage = 'Temperature settings for Bmp sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $minMessage = 'Temperature settings for Bmp sensor cannot be below '. Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
