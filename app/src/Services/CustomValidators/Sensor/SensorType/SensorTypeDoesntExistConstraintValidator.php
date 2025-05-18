<?php

namespace App\Services\CustomValidators\Sensor\SensorType;

use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SensorTypeDoesntExistConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly SensorTypeRepository $sensorTypeRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SensorTypeDoesntExistConstraint) {
            throw new UnexpectedTypeException($value, SensorTypeDoesntExistConstraint::class);
        }

        if ($this->sensorTypeRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ sensorType }}', $value)
                ->addViolation();
        }
    }
}
