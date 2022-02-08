<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use JetBrains\PhpStorm\Pure;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function buildStandardSensorUpdateReadingDTO(
        array $sensorData,
        StandardReadingSensorInterface $standardReadingSensor
    ): UpdateStandardSensorBoundaryReadingsDTO
    {
        return new UpdateStandardSensorBoundaryReadingsDTO(
            $sensorData['readingType'],
            $standardReadingSensor->getHighReading(),
            $standardReadingSensor->getLowReading(),
            $standardReadingSensor->getConstRecord(),
            $sensorData['highReading'] ?? null,
            $sensorData['lowReading'] ?? null,
            $sensorData['constRecord'] ?? null
        );
    }

    protected function updateStandardSensor(StandardReadingSensorInterface $standardReadingSensor, UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }
}
