<?php

namespace App\Services\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\SensorTypes\Sht;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SHTTemperatureConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SHTTemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, DHTTemperatureConstraint::class);
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

        if (is_numeric($value) && $value > Sht::HIGH_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value < Sht::LOW_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
