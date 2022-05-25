<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function buildStandardSensorUpdateReadingDTO(
        StandardSensorUpdateBoundaryDataDTO $sensorData,
        StandardReadingSensorInterface $standardReadingSensor
    ): UpdateStandardReadingTypeBoundaryReadingsDTO {
        return new UpdateStandardReadingTypeBoundaryReadingsDTO(
            $sensorData->getReadingType(),
            $standardReadingSensor->getHighReading(),
            $standardReadingSensor->getLowReading(),
            $standardReadingSensor->getConstRecord(),
            $sensorData->getHighReading(),
            $sensorData->getLowReading(),
            $sensorData->getConstRecord()
        );
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
