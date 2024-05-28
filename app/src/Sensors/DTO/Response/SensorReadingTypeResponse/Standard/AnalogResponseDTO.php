<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class AnalogResponseDTO extends AbstractStandardResponseDTO implements AllSensorReadingTypeResponseDTOInterface, StandardReadingTypeResponseInterface
{
    public function __construct(
        private int $analogID,
        int $baseReadingTypeID,
        SensorResponseDTO $sensor,
        float $currentReading,
        float $highReading,
        float $lowReading,
        bool $constRecorded,
        string $updatedAt
    ) {
        $type = Analog::READING_TYPE;
        parent::__construct(
            sensor: $sensor,
            baseReadingTypeID: $baseReadingTypeID,
            currentReading: $currentReading,
            highReading: $highReading,
            lowReading: $lowReading,
            constRecord: $constRecorded,
            updated: $updatedAt,
            readingType: $type
        );
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getAnalogID(): int
    {
        return $this->analogID;
    }

}
