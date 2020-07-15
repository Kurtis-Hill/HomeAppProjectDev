<?php


namespace App\Form\CustomFormValidators;

use Symfony\Component\Validator\Constraint;

class DHTTemperatureConstraint extends Constraint
{
    public $minMessage = 'Temperature settings for DHT sensor cannot be below -40°C  you entered "{{ string }}" entered';

    public $maxMessage = 'Temperature settings for DHT sensor cannot exceed 80°C "{{ string }}" entered';

    public $intMessage = 'The submitted value is not a number "{{ string }}"';

}