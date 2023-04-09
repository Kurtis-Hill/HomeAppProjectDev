<?php

namespace App\Common\Validation\Traits;

use App\Common\Exceptions\ValidatorProcessorException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidatorProcessorTrait
{
    public function checkIfErrorsArePresent(ConstraintViolationListInterface $constraintViolationList): bool
    {
        return count($constraintViolationList) > 0;
    }

    /**
     * @throws ValidatorProcessorException
     */
    public function getValidationErrorAsArray(ConstraintViolationListInterface $constraintViolationList): array
    {
        foreach ($constraintViolationList as $error) {
            $validationErrors[] = $error->getMessage();
        }

        return $validationErrors ?? [];
    }

    /**
     * @throws ValidatorProcessorException
     */
    public function getValidationErrorsAsStrings(ConstraintViolation $constraintViolation): string
    {
        return $constraintViolation->getMessage();
    }
}
