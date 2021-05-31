<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;

use App\Entity\Sensors\ReadingTypes\Humidity;
use Symfony\Component\Validator\Constraint;

class HumidityConstraint extends Constraint
{
    public $minMessage = 'Humidity for this sensor cannot be under 0 you entered {{ string }}'. Humidity::READING_SYMBOL;

    public $maxMessage = 'Humidity for this sensor cannot be over 100 you entered {{ string }}'. Humidity::READING_SYMBOL;

    public $intMessage = 'The submitted value is not a number "{{ string }}"';
}
