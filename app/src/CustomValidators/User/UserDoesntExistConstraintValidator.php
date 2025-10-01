<?php

namespace App\CustomValidators\User;

use App\Repository\User\ORM\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserDoesntExistConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserDoesntExistConstraint) {
            throw new UnexpectedTypeException($value, UserDoesntExistConstraint::class);
        }

        if (empty($value)) {
            return;
        }

        if ($this->userRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ user }}', $value)
                ->addViolation();
        }
    }
}
