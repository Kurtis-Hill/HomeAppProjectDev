<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LatitudeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LatitudeConstraint) {
            throw new UnexpectedTypeException($constraint, LatitudeConstraint::class);        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_numeric($value)) {
            $this->context->buildViolation($constraint->intMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value > 90) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < 0) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
