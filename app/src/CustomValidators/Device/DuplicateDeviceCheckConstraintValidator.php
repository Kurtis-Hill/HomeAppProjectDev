<?php

namespace App\CustomValidators\Device;

use App\Services\Device\DuplicateDeviceChecker;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DuplicateDeviceCheckConstraintValidator extends ConstraintValidator
{
    public function __construct(private DuplicateDeviceChecker $duplicateDeviceChecker)
    {

    }

    public function validate($value, $constraint)
    {
//        if (!$constraint instanceof DuplicateDeviceCheckConstraint) {
//            throw new UnexpectedTypeException($constraint, DuplicateDeviceCheckConstraint::class);
//        }
//
//        if ($this->duplicateDeviceChecker->duplicateDeviceCheck($value)) {
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('{{ value }}', $value)
//                ->addViolation();
//        }
    }
}
