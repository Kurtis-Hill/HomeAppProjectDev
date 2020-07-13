<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DHTTemperatureConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DHTTemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, DHTTemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($value > 80) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
//                ->atPath('[type]')
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < -40) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
//                ->atPath('[type]')
                ->setInvalidValue($value)
                ->addViolation();
        }
    }

}