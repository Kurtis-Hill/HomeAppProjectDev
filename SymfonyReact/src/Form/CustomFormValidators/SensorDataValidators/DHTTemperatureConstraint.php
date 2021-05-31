<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;

use App\Entity\Sensors\ReadingTypes\Temperature;
use Symfony\Component\Validator\Constraint;

class DHTTemperatureConstraint extends Constraint
{
    public $minMessage = 'Temperature settings for DHT sensor cannot be below -40' . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public $maxMessage = 'Temperature settings for DHT sensor cannot exceed 80' . Temperature::READING_SYMBOL . ' you entered {{ string }} entered'. Temperature::READING_SYMBOL;

    public $intMessage = 'The submitted value is not a number "{{ string }}"';

}
