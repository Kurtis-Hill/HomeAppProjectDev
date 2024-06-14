<?php

namespace App\Services\CustomValidators;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoSpecialCharactersNameConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoSpecialCharactersNameConstraint) {
            throw new UnexpectedTypeException($constraint, NoSpecialCharactersNameConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (preg_match("/[^A-Za-z\d.-]/", $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
