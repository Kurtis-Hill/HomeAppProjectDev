<?php

namespace App\API\Traits;

use App\User\Entity\Room;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ObjectValidatorTrait
{
    protected function validateObjectReturnErrorsArray(ValidatorInterface $validator, Room $room): array
    {
        $errors = $validator->validate($room);

        if (count($errors) > 0) {
            $validationErrors = [];
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }
        }

        return $validationErrors ?? [];
    }
}
