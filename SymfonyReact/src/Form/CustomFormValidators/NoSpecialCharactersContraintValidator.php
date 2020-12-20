<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoSpecialCharactersContraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NoSpecialCharactersContraint) {
            throw new UnexpectedTypeException($constraint, NoSpecialCharactersContraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (preg_match("/[^A-Za-z0-9]/", $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}