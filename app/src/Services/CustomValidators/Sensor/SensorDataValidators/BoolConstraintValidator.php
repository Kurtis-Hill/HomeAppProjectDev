<?php

namespace App\Services\CustomValidators\Sensor\SensorDataValidators;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BoolConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof BoolConstraint) {
            throw new UnexpectedTypeException($constraint, LatitudeConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_bool($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', gettype($value))
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
