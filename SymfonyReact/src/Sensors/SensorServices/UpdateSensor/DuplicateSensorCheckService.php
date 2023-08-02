<?php

namespace App\Sensors\SensorServices\UpdateSensor;

use App\Devices\Repository\ORM\DeviceRepository;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;

class DuplicateSensorCheckService
{
    private SensorRepositoryInterface $sensorRepository;

    public function __construct(SensorRepositoryInterface $sensorRepository, DeviceRepository $deviceRepository)
    {
        $this->sensorRepository = $sensorRepository;
    }

    /**
     * @throws DuplicateSensorException
     */
    public function checkSensorForDuplicates(
        Sensor $sensor,
        int $deviceID,
        bool $isNewSensor = false,
        string $sensorNameToUpdateTo = null,
        int $pinToUpdateTo = null,
    ): void {
        $sensorID = $isNewSensor === true
            ? null
            : $sensor->getSensorID();

        $duplicateSensorCheck = $this->sensorRepository->findSensorObjectByDeviceIdAndSensorName(
            $deviceID,
            $sensorNameToUpdateTo ?? $sensor->getSensorName(),
        );
        if (
            $duplicateSensorCheck instanceof Sensor
            && $duplicateSensorCheck->getSensorID() !== $sensorID
        ) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE,
                    $sensorNameToUpdateTo ?? $sensor->getSensorName(),
                )
            );
        }

        $currentUserSensorNameCheck = $this->sensorRepository->findDuplicateSensorOnDeviceByGroup($sensor);
        if (
            $currentUserSensorNameCheck instanceof Sensor
            && $currentUserSensorNameCheck->getSensorID() !== $sensorID
        ) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE_GROUP,
                    $sensor->getSensorName(),
                    $sensor->getDevice()->getGroupObject()->getGroupName()
                )
            );
        }

        $pinCheck = $this->sensorRepository->findSensorObjectByDeviceIDAndPinNumber($deviceID, $pinToUpdateTo ?? $sensor->getPinNumber());
        if ($pinCheck instanceof Sensor && $pinCheck->getSensorID() !== $sensorID) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE_PIN,
                    $pinToUpdateTo ?? $sensor->getPinNumber()
                )
            );
        }
    }
}
