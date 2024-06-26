<?php

namespace App\Services\Sensor\UpdateSensor;

use App\DTOs\Sensor\Internal\Sensor\UpdateSensorDTO;
use App\Traits\ValidatorProcessorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class UpdateSensorHandler implements UpdateSensorInterface
{
    use ValidatorProcessorTrait;

    public function __construct(
        private DuplicateSensorCheckService $duplicateSensorCheckService,
        private ValidatorInterface $validator,
    ) {}

    public function handleSensorUpdate(UpdateSensorDTO $updateSensorDTO): array
    {
        $this->duplicateSensorCheckService->checkSensorForDuplicates(
            sensor: $updateSensorDTO->getSensor(),
            deviceID: $updateSensorDTO->getDeviceID()?->getDeviceID() ?? $updateSensorDTO->getSensor()->getDevice()->getDeviceID(),
            sensorNameToUpdateTo: $updateSensorDTO->getSensorName(),
            pinToUpdateTo: $updateSensorDTO->getPinNumber()
        );

        $sensorToUpdate = $updateSensorDTO->getSensor();
        if ($updateSensorDTO->getDeviceID() !== null) {
            $sensorToUpdate->setDevice($updateSensorDTO->getDeviceID());
        }

        if ($updateSensorDTO->getSensorName() !== null) {
            $sensorToUpdate->setSensorName($updateSensorDTO->getSensorName());
        }

        if ($updateSensorDTO->getPinNumber() !== null) {
            $sensorToUpdate->setPinNumber($updateSensorDTO->getPinNumber());
        }

        if ($updateSensorDTO->getReadingInterval() !== null) {
            $sensorToUpdate->setReadingInterval($updateSensorDTO->getReadingInterval());
        }

        $validationErrors = $this->validator->validate($sensorToUpdate);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            $this->getValidationErrorAsArray($validationErrors);
        }

        return [];
    }
}
