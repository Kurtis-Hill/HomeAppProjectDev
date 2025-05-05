<?php

namespace App\Services\CustomValidators\Device;

use App\DTOs\Sensor\Request\CanAdjustSensorDeviceIDAndSensorNameInterface;
use App\Repository\Device\ORM\DeviceRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class DeviceIDExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly DeviceRepository $deviceRepository)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DeviceIDExists) {
            throw new UnexpectedTypeException($constraint, DeviceIDExists::class);
        }

        if (!$value ) {
            throw new UnexpectedTypeException($value, CanAdjustSensorDeviceIDAndSensorNameInterface::class);
        }

//        $deviceID = $value->getDeviceID();

        if ($value !== null && !$this->deviceRepository->find($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ deviceID }}', (string) $value)
                ->addViolation();
        }
    }
}
