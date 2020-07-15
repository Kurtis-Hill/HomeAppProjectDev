<?php


namespace App\Form\CustomFormValidators;

use Symfony\Component\Validator\Constraint;

class DHTHumidityConstraint extends Constraint
{
    public $minMessage = 'Humidity for this sensor cannot be under 0 you entered "{{ string }}"';

    public $maxMessage = 'Humidity for this sensor cannot be over 100 you entered "{{ string }}"';

    public $intMessage = 'The submitted value is not a number "{{ string }}"';
}