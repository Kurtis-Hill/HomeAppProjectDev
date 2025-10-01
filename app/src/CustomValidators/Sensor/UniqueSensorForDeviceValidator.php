<?php

namespace App\CustomValidators\Sensor;

use App\Services\Sensor\UpdateSensor\DuplicateSensorCheckService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\DTOs\Sensor\Request\CanAdjustSensorDeviceIDAndSensorNameInterface;

class UniqueSensorForDeviceValidator extends ConstraintValidator
{
    public function __construct(private readonly DuplicateSensorCheckService $duplicateSensorCheckService)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueSensorForDevice) {
            throw new UnexpectedTypeException($value, UniqueSensorForDevice::class);
        }

        if (!$value instanceof CanAdjustSensorDeviceIDAndSensorNameInterface) {
            throw new UnexpectedTypeException($value, CanAdjustSensorDeviceIDAndSensorNameInterface::class);
        }

        $sensorName = $value->getSensorName();
        $deviceID = $value->getDeviceID();

        // Skip validation if either field is null (e.g., during partial updates)
        if ($sensorName === null || $deviceID === null) {
            return;
        }

        $existingSensor = $this->duplicateSensorCheckService->checkSensorForDuplicatesByDeviceIDAndSensorName(
            $sensorName,
            $deviceID,
        );

        if ($existingSensor) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ sensorName }}', $sensorName)
                ->setParameter('{{ deviceID }}', (string)$deviceID)
                ->addViolation();
        }

    }
}
