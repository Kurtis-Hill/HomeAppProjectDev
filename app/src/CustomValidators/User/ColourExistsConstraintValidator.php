<?php

namespace App\CustomValidators\User;

use App\Repository\UserInterface\ORM\CardRepositories\CardColourRepository;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ColourExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly CardColourRepository $cardColourRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ColourExistsConstraint) {
            throw new UnexpectedTypeException($value, ColourExistsConstraint::class);
        }
        if ($value === null) {
            return;
        }

        if ($this->cardColourRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ colour }}', $value)
                ->addViolation();
        }
    }
}
