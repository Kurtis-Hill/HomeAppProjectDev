<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Sensor\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Sensor\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function buildStandardSensorUpdateReadingDTO(
        array $sensorData,
        StandardReadingSensorInterface $standardReadingSensor
    ): UpdateStandardReadingTypeBoundaryReadingsDTO {
        return new UpdateStandardReadingTypeBoundaryReadingsDTO(
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
        UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
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
        string $newSensorReading,
    ): ReadingTypeUpdateCurrentReadingDTO {
        return new ReadingTypeUpdateCurrentReadingDTO(
            $standardReadingSensor->getCurrentReading(),
            $newSensorReading,
            $standardReadingSensor
        );
    }
}
