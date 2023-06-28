<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders\Bool;

use App\Sensors\DTO\Internal\BoundaryReadings\UpdateBoolReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;

class AbstractBoolSensorUpdateBuilder
{
    protected function buildBoolUpdateSensorBoundaryReadingsDTO(
        BoolSensorUpdateBoundaryDataDTO $boolSensorUpdateBoundaryDataDTO,
        BoolReadingSensorInterface $boolReadingSensor
    ): UpdateBoolReadingTypeBoundaryReadingsDTO {
        return new UpdateBoolReadingTypeBoundaryReadingsDTO(
            $boolSensorUpdateBoundaryDataDTO->getReadingType(),
            $boolReadingSensor->getExpectedReading(),
            $boolReadingSensor->getConstRecord(),
            $boolSensorUpdateBoundaryDataDTO->getExpectedReading(),
            $boolSensorUpdateBoundaryDataDTO->getConstRecord()
        );
    }
}
