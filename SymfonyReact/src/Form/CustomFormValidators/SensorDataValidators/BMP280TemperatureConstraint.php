<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use App\Entity\Sensors\ReadingTypes\Temperature;
use Symfony\Component\Validator\Constraint;

class BMP280TemperatureConstraint extends Constraint
{
    public $minMessage = 'Temperature for this sensor cannot be under -55' . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public $maxMessage = 'Temperature for this sensor cannot be over 125' . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public $intMessage = 'The submitted value is not a number "{{ string }}"';
}
