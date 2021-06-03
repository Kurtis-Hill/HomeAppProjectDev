<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorTypes\Bmp;
use Symfony\Component\Validator\Constraint;

class BMP280TemperatureConstraint extends Constraint
{
    public string $maxMessage = 'Temperature settings for Bmp sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $minMessage = 'Temperature settings for Bmp sensor cannot be below '. Bmp::LOW_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
