<?php

namespace App\Services\Sensor\UpdateSensor;

use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\DuplicateSensorException;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;

readonly class DuplicateSensorCheckService
{
    public function __construct(private SensorRepositoryInterface $sensorRepository)
    {
    }

    public function checkSensorForDuplicatesByDeviceIDAndSensorName(
        string $sensorName,
        int $deviceID,
    ): bool {
        $duplicateSensorCheck = $this->sensorRepository->findSensorObjectByDeviceIdAndSensorName(
            $deviceID,
            $sensorName,
        );

        if ($duplicateSensorCheck instanceof Sensor) {
            return true;
        }

        return false;
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

// had to disable due to analog pins and digital pins using the same pin number
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
