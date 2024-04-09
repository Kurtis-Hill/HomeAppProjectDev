<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class MotionResponseDTO extends AbstractBoolResponseDTO implements AllSensorReadingTypeResponseDTOInterface, BoolReadingTypeResponseInterface
{
    public function __construct(
        SensorResponseDTO $sensorResponseDTO,
        int $baseReadingTypeID,
        int $boolID,
        bool $currentReading,
        bool $requestedReading,
        bool $constRecord,
        string $updatedAt,
        ?bool $expectedReading = null,
    ) {
        parent::__construct(
            sensor: $sensorResponseDTO,
            baseReadingTypeID: $baseReadingTypeID,
            boolID: $boolID,
            currentReading: $currentReading,
            requestedReading: $requestedReading,
            constRecord: $constRecord,
            updatedAt: $updatedAt,
            readingType: Motion::READING_TYPE,
            expectedReading: $expectedReading
        );
    }
}
