<?php

namespace App\CustomValidators\Authenication;

use App\Repository\Authentication\ORM\GroupMappingRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GroupMappingExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly GroupMappingRepository $groupMappingRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof GroupMappingExistsConstraint) {
            throw new UnexpectedTypeException($value, GroupMappingExistsConstraint::class);
        }

        if (empty($value)) {
            return;
        }

        if ($this->groupMappingRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ groupMapping }}', $value)
                ->addViolation();
        }
    }
}
