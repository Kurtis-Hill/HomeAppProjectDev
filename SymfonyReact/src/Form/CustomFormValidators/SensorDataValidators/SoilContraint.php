<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use Symfony\Component\Validator\Constraint;

class SoilContraint extends Constraint
{
    public $maxMessage = 'Reading for this sensor cannot be over 9999 you entered "{{ string }}"';

    public $minMessage = 'Reading for this sensor cannot be under 1000 you entered "{{ string }}"';

    public $intMessage = 'The submitted value is not a number "{{ string }}"';
}
