<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use Symfony\Component\Validator\Constraint;

class LatitudeConstraint extends Constraint
{
    public string $maxMessage = 'The highest possible latitude is 90 you entered "{{ string }}"';

    public string $minMessage = 'The lowest possible latitude is 0 you entered "{{ string }}"';

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';
}
