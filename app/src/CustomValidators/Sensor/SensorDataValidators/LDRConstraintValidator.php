<?php

namespace App\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\SensorTypes\LDR;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LDRConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LDRConstraint) {
            throw new UnexpectedTypeException($constraint,SoilConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_numeric($value)) {
            $this->context->buildViolation($constraint->intMessage)
                ->setParameter('{{ string }}', gettype($value))
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value > LDR::HIGH_READING) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value < LDR::LOW_READING) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
