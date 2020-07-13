<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DHTTemperatureConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $value = (int)$value;
        //dd($value);
        if (!$constraint instanceof DHTTemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, DHTTemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }


        if ($value > 85) {
            $this->context->buildViolation($constraint->message)
//                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

}