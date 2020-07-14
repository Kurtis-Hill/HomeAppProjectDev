<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DallasTemperatureConstraintValidation extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DHTTemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, DallasTemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($value > 125) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < -55) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}