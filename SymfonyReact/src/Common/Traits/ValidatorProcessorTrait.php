<?php

namespace App\Common\Traits;

use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidatorProcessorTrait
{
    public function checkIfErrorsArePresent(ConstraintViolationListInterface $constraintViolationList): bool
    {
        return count($constraintViolationList) > 0;
    }

    public function returnValidationErrorAsArray(ConstraintViolationListInterface $constraintViolationList): array
    {
        foreach ($constraintViolationList as $error) {
            $validationErrors[] = $error->getMessage();
        }

        return $validationErrors ?? [];
    }
}
