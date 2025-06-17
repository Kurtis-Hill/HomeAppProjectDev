<?php

namespace App\CustomValidators\Card;

use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CardStateExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly  CardStateRepository $cardStateRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CardStateExistsConstraint) {
            throw new UnexpectedTypeException($value, CardStateExistsConstraint::class);
        }
        if ($value === null) {
            return;
        }

        if ($this->cardStateRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ cardState }}', $value)
                ->addViolation();
        }
    }
}
