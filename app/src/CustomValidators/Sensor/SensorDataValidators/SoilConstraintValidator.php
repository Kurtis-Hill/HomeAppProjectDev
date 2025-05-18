<?php

namespace App\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\SensorTypes\Soil;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SoilConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SoilConstraint) {
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

        if (is_numeric($value) && $value > Soil::HIGH_SOIL_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value < Soil::LOW_SOIL_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
