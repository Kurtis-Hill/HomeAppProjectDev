<?php

namespace App\CustomValidators\Sensor;

use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SensorExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly SensorRepository $sensorRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SensorExistsConstraint) {
            throw new UnexpectedTypeException($value, SensorExistsConstraint::class);
        }

        // Assuming $this->sensorRepository is injected and available
        if ($this->sensorRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ sensorID }}', $value)
                ->addViolation();
        }
    }
}
