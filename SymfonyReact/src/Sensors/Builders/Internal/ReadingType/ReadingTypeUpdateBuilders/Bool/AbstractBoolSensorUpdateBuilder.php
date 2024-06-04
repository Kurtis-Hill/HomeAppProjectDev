<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders\Bool;

use App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders\AbstractCurrentReadingUpdateBuilder;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateBoolReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;

class AbstractBoolSensorUpdateBuilder extends AbstractCurrentReadingUpdateBuilder
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

    public function buildBoolRequestCurrentReadingUpdateDTO(mixed $currentReading, string $readingType): AbstractCurrentReadingUpdateRequestDTO
    {
        return new BoolCurrentReadingUpdateRequestDTO($currentReading, $readingType);
    }
}
