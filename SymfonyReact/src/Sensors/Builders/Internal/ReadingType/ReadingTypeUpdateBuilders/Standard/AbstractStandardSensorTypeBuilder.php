<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard;

use App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders\AbstractCurrentReadingUpdateBuilder;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;

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
