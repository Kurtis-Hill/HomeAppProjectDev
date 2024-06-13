<?php

namespace App\Services\Sensor\UpdateSensor;

use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\DuplicateSensorException;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;

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

//        $pinCheck = $this->sensorRepository->findSensorsObjectByDeviceIDAndPinNumber($deviceID, $pinToUpdateTo ?? $sensor->getPinNumber());
//        if ($pinCheck instanceof Sensor && $pinCheck->getSensorID() !== $sensorID) {
//            throw new DuplicateSensorException(
//                sprintf(
//                    DuplicateSensorException::MESSAGE_PIN,
//                    $pinToUpdateTo ?? $sensor->getPinNumber()
//                )
//            );
//        }
    }
}
