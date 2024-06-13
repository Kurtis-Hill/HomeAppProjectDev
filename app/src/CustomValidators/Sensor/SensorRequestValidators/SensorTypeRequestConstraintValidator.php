<?php

namespace App\CustomValidators\Sensor\SensorRequestValidators;

use App\Entity\Sensor\AbstractSensorType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SensorTypeRequestConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SensorTypeRequestConstraint) {
            throw new UnexpectedTypeException($constraint, SensorTypeRequestConstraint::class);
        }

        if (empty($value)) {
            return;
        }

        $missingSensorTypes = array_diff(
            AbstractSensorType::ALL_SENSOR_TYPES,
            $value
        );

        if (empty($missingSensorTypes)) {
            $this->context->buildViolation($constraint->allSensorTypeFilteredMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
