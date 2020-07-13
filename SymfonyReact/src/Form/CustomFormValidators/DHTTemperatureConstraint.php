<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;


class DHTTemperatureConstraint extends Constraint
{
    public $message = "Temperature settings for DHT sensor must be between 5oC and 80oC";

}