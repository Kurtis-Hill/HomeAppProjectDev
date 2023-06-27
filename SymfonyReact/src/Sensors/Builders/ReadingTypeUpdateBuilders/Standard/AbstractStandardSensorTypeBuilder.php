<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard;

use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

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

    protected function buildStandardSensorUpdateCurrentReadingDTO(
        AllSensorReadingTypeInterface $standardReadingSensor,
        string $newSensorReading,
    ): ReadingTypeUpdateCurrentReadingDTO {
        return new ReadingTypeUpdateCurrentReadingDTO(
            $newSensorReading,
            $standardReadingSensor->getCurrentReading(),
            $standardReadingSensor
        );
    }
}
