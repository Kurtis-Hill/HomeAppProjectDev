<?php

namespace App\CustomValidators\User;

use App\Repository\UserInterface\ORM\IconsRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IconExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly IconsRepository $iconsRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IconExistsConstraint) {
            throw new UnexpectedTypeException($value, IconExistsConstraint::class);
        }
        if ($value === null) {
            return;
        }

        if ($this->iconsRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ icon }}', $value)
                ->addViolation();
        }
    }
}
