<?php

namespace App\Sensors\SensorServices\UpdateSensor;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Builders\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DeviceNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateSensorHandler implements UpdateSensorInterface
{
    use ValidatorProcessorTrait;

    private DuplicateSensorCheckService $duplicateSensorCheckService;

    private DeviceRepositoryInterface $deviceRepository;

    private ValidatorInterface $validator;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        DuplicateSensorCheckService $duplicateSensorCheckService,
        ValidatorInterface $validator,
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->duplicateSensorCheckService = $duplicateSensorCheckService;
        $this->validator = $validator;
    }

    /**
     * @throws DeviceNotFoundException
     */
    public function buildSensorUpdateDTO(SensorUpdateRequestDTO $sensorUpdateRequestDTO, Sensor $sensor): UpdateSensorDTO
    {
        if ($sensorUpdateRequestDTO->getDeviceID() !== null) {
            $proposedDevice = $this->deviceRepository->find($sensorUpdateRequestDTO->getDeviceID());
            if ($proposedDevice === null) {
                throw new DeviceNotFoundException(
                    sprintf(
                        DeviceNotFoundException::DEVICE_NOT_FOUND_FOR_ID,
                        $sensorUpdateRequestDTO->getDeviceID()
                    )
                );
            }
        }

        return SensorUpdateDTOBuilder::buildSensorUpdateDTO(
            $sensor,
            $sensorUpdateRequestDTO->getSensorName(),
            $proposedDevice ?? null,
            $sensorUpdateRequestDTO->getPinNumber(),
            $sensorUpdateRequestDTO->getReadingInterval(),
        );
    }

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
