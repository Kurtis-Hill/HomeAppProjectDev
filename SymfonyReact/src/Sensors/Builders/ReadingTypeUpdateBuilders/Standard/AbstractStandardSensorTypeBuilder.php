<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\AbstractCurrentReadingUpdateBuilder;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

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
