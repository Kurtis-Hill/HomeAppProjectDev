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
        UpdateSensorDTO $updateSensorDTO
    ): void {
        $sensorToUpdate = $updateSensorDTO->getSensor();
        $duplicateSensorCheck = $this->sensorRepository->findSensorObjectByDeviceIdAndSensorName(
            $updateSensorDTO->getDeviceID()?->getDeviceID() ?? $sensorToUpdate->getDevice()->getDeviceID(),
            $updateSensorDTO->getSensorName() ?? $sensorToUpdate->getSensorName()
        );

        if (
            $duplicateSensorCheck instanceof Sensor
            && $duplicateSensorCheck->getSensorID() !== $sensorToUpdate->getSensorID()
        ) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE,
                    $updateSensorDTO->getSensorName(),
                )
            );
        }
    }
}
