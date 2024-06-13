<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Bool;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\AbstractCurrentReadingUpdateBuilder;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateBoolReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;

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
