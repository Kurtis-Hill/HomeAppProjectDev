<?php

namespace App\Sensors\Builders\SensorUpdateRequestDTOBuilder;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\BusSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;

class BusSensorUpdateRequestDTOBuilder implements SensorUpdateRequestDTOBuilderInterface
{
    public function __construct(
        private readonly SensorRepositoryInterface $sensorRepository,
    ) {}

    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface
    {
        $allBusSensors = $this->sensorRepository->findAllBusSensors(
            $sensor->getDevice()->getDeviceID(),
            $sensor->getSensorID(),
            $sensor->getSensorID(),
        );

        $sensorNames = array_map(
            static fn (Sensor $sensor) => $sensor->getSensorName(),
            $allBusSensors,
        );

        return new BusSensorUpdateRequestDTO(
            $sensorNames,
            $sensor->getPinNumber(),
            count($allBusSensors),
            $sensor->getReadingInterval(),
        );
    }
}
