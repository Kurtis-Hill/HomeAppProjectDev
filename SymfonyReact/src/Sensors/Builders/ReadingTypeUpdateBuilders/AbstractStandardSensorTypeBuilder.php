<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateReadingTypeCurrentReadingDTO;
use App\Sensors\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function buildStandardSensorUpdateReadingDTO(
        array $sensorData,
        StandardReadingSensorInterface $standardReadingSensor
    ): UpdateStandardSensorBoundaryReadingsDTO {
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

    protected function updateStandardSensor(
        StandardReadingSensorInterface $standardReadingSensor,
        UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void {
        if ($updateSensorBoundaryReadingsDTO->getHighReading() !== null) {
            $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        }
        if ($updateSensorBoundaryReadingsDTO->getLowReading() !== null) {
            $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        }
        if ($updateSensorBoundaryReadingsDTO->getConstRecord() !== null) {
            $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
        }
    }

    protected function updateStandardSensorCurrentReading(
        AllSensorReadingTypeInterface $standardReadingSensor,
        string $reading
    ): UpdateReadingTypeCurrentReadingDTO
    {
        return new UpdateReadingTypeCurrentReadingDTO(
            $standardReadingSensor->getCurrentReading(),
            $reading,
            $standardReadingSensor
        );
    }
}
