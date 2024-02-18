<?php

namespace App\Sensors\SensorServices\Sensor;

use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;

class DuplicateSensorCheckService
{
    private SensorRepositoryInterface $sensorRepository;

    public function __construct(SensorRepositoryInterface $sensorRepository)
    {
        $this->sensorRepository = $sensorRepository;
    }

    /**
     * @throws DuplicateSensorException
     */
    public function checkSensorForDuplicates(
        Sensor $sensor,
        int $deviceID,
        string $sensorNameToUpdateTo = null,
    ): void {
        $duplicateSensorCheck = $this->sensorRepository->findSensorObjectByDeviceIdAndSensorName(
            $deviceID,
            $sensorNameToUpdateTo ?? $sensor->getSensorName(),
        );

        if (
            $duplicateSensorCheck instanceof Sensor
            && $duplicateSensorCheck->getSensorID() !== $sensor->getSensorID()
        ) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE,
                    $sensorNameToUpdateTo ?? $sensor->getSensorName(),
                )
            );
        }
    }
}
