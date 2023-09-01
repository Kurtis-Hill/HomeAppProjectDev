<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class RelayResponseDTO extends AbstractBoolResponseDTO implements AllSensorReadingTypeResponseDTOInterface, SensorReadingTypeResponseDTOInterface, BoolReadingTypeResponseInterface
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
        $type = Relay::READING_TYPE;

        parent::__construct(
            sensor: $sensorResponseDTO,
            boolID: $boolID,
            currentReading: $currentReading,
            requestedReading: $requestedReading,
            constRecord: $constRecord,
            updatedAt: $updatedAt,
            readingType: $type,
            expectedReading: $expectedReading
        );
    }
}
