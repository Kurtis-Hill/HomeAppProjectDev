<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;

class SoilContraint extends Constraint
{
    public $minMessage = 'Humidity for this sensor cannot be under 1000 you entered "{{ string }}"';

    public $maxMessage = 'Humidity for this sensor cannot be over 9999 you entered "{{ string }}"';

    public $intMessage = 'The submitted value is not a number "{{ string }}"';
}