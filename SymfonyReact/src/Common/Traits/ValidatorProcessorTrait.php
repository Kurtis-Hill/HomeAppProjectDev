<?php

namespace App\Common\Traits;

use Generator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidatorProcessorTrait
{
    public function checkIfErrorsArePresent(ConstraintViolationListInterface $constraintViolationList): bool
    {
        return count($constraintViolationList) > 0;
    }

    public function getValidationErrorAsArray(ConstraintViolationListInterface $constraintViolationList): array
    {
        foreach ($constraintViolationList as $error) {
            $validationErrors[] = $error->getMessage();
        }

        return $validationErrors ?? [];
    }

    public function getValidationErrorsAsStrings(ConstraintViolation $constraintViolation): Generator
    {
        yield $constraintViolation->getMessage();
    }
}
