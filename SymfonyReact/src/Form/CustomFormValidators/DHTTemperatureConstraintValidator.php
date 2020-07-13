<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DHTTemperatureValidator extends ConstraintValidator
{
    public $message;


    public function validate($value, Constraint $constraint)
    {
        dd($value);

        if ($value === null || $value === '') {
            return;
        }
        dd($value);
    }
}