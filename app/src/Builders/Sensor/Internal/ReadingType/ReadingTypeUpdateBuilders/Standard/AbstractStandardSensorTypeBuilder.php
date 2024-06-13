<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\AbstractCurrentReadingUpdateBuilder;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder extends AbstractCurrentReadingUpdateBuilder
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
}
