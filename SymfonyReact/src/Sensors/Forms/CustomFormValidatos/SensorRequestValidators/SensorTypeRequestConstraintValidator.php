<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorRequestValidators;

use App\Sensors\Entity\SensorType;
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
            SensorType::ALL_SENSOR_TYPES,
            $value
        );

        if (empty($missingSensorTypes)) {
            $this->context->buildViolation($constraint->allSensorTypeFilteredMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
