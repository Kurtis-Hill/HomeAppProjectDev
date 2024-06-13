<?php

namespace App\Traits;

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

    public function getValidationErrorsAsStrings(ConstraintViolation $constraintViolation): string
    {
        return $constraintViolation->getMessage();
    }
}
