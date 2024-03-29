<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class MotionResponseDTO extends AbstractBoolResponseDTO implements AllSensorReadingTypeResponseDTOInterface, SensorReadingTypeResponseDTOInterface, BoolReadingTypeResponseInterface
{
    public function __construct(
        SensorResponseDTO $sensorResponseDTO,
        int $boolID,
        bool $currentReading,
        bool $requestedReading,
        bool $constRecord,
        string $updatedAt,
        ?bool $expectedReading = null,
    ) {
        $type = Motion::READING_TYPE;

        parent::__construct(
            sensor: $sensorResponseDTO,
            boolID: $boolID,
            currentReading: $currentReading,
            requestedReading: $requestedReading,
            expectedReading: $expectedReading,
            constRecord: $constRecord,
            updatedAt: $updatedAt,
            readingType: $type
        );
    }
}
