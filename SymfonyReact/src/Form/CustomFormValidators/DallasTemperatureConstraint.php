<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;

class DallasTemperatureConstraint extends Constraint
{
    public $minMessage = 'Temperature for this sensor cannot be under -55°C you entered "{{ string }}"';

    public $maxMessage = 'Temperature for this sensor cannot be over 125°C you entered "{{ string }}"';

    public $intMessage = 'The submitted vvalue is not a number';
}