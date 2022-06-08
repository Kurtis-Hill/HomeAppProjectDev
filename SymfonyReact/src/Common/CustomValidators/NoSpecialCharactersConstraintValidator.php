<?php


namespace App\Common\CustomValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoSpecialCharactersConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoSpecialCharactersConstraint) {
            throw new UnexpectedTypeException($constraint, NoSpecialCharactersConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (preg_match("/[^A-Za-z\d.]/", $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
