<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorRequestValidators;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class ReadingTypeRequestConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReadingTypeRequestConstraint) {
            throw new UnexpectedTypeException($constraint, ReadingTypeRequestConstraint::class);
        }
        if (empty($value)) {
            return;
        }

        $missingSensorTypes = array_diff(
            ReadingTypes::ALL_READING_TYPES,
            $value
        );

        if (empty($missingSensorTypes)) {
            $this->context->buildViolation($constraint->allReadingTypeFilteredMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
{

}
