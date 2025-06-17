<?php

namespace App\CustomValidators\Sensor\SensorRequestValidators;

use App\Entity\Sensor\ReadingTypes\ReadingTypeEnum;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReadingTypeRequestConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReadingTypeRequestConstraint) {
            throw new UnexpectedTypeException($constraint, ReadingTypeRequestConstraint::class);
        }
        if (empty($value)) {
            return;
        }

        $missingSensorTypes = array_diff(
            ReadingTypeEnum::values(),
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
