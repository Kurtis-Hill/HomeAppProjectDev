<?php


namespace App\Form\CustomFormValidators\SensorDataValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BMP280TemperatureConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BMP280TemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, BMP280TemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_numeric($value)) {
            $this->context->buildViolation($constraint->intMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value > 85) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < -40) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
