<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DHTHumidityConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DHTHumidityConstraint) {
            throw new UnexpectedTypeException($constraint,DHTHumidityConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_int((int)$value)) {
             throw new InvalidTypeException($constraint);
        }

        //$value = (int) $value;

//        if (!preg_match('/[^0-9]+/', $value)) {
//          //  dd('violation');
//            $this->context->buildViolation($constraint->intMessage)
//                ->setParameter('{{ string }}', $value)
//                ->setInvalidValue($value)
//                ->addViolation();
//        }

        if ($value > 100) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < 0) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}