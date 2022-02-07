<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use JetBrains\PhpStorm\Pure;

abstract class AbstractStandardSensorTypeBuilder
{
    #[Pure]
    protected function buildStandardSensorUpdateReadingDTO(array $sensorData, StandardReadingSensorInterface $standardReadingSensor): UpdateSensorBoundaryReadingsDTO
    {
        return new UpdateSensorBoundaryReadingsDTO(
            $sensorData['readingType'],
            $sensorData['highReading'],
            $sensorData['lowReading'],
            $sensorData['constRecord'],
            $standardReadingSensor->getHighReading(),
            $standardReadingSensor->getLowReading(),
        );
    }

    protected function updateStandardSensor(StandardReadingSensorInterface $standardReadingSensor, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }
}
