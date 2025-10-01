<?php

namespace App\CustomValidators\User;

use App\Repository\User\ORM\GroupRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GroupExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private  readonly GroupRepository $groupRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof GroupExistsConstraint) {
            throw new UnexpectedTypeException($value, GroupExistsConstraint::class);
        }

        if ($value === null) {
            return;
        }

        if ($this->groupRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ group }}', $value)
                ->addViolation();
        }
    }
}
