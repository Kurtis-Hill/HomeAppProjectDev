<?php

namespace App\Services\CustomValidators;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotNumericConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotNumericConstraint) {
            throw new UnexpectedTypeException($constraint, NotNumericConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (preg_match("/\d/", $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
