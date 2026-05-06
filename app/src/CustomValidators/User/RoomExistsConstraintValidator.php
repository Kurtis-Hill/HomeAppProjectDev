<?php

namespace App\CustomValidators\User;

use App\Repository\User\ORM\RoomRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RoomExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly RoomRepository $roomRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RoomExistsConstraint) {
            throw new UnexpectedTypeException($value, RoomExistsConstraint::class);
        }

        if ($value === null) {
            return;
        }

        if ($this->roomRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ room }}', $value)
                ->addViolation();
        }
    }
}
