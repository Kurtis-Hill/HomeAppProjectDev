<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class HumidityResponseDTO extends AbstractStandardResponseDTO implements AllSensorReadingTypeResponseDTOInterface, StandardReadingTypeResponseInterface
{
    public function __construct(
        private int $humidityID,
        int $baseReadingTypeID,
        SensorResponseDTO $sensor,
        float $currentReading,
        float $highReading,
        float $lowReading,
        bool $constRecorded,
        string $updatedAt
    ) {
        parent::__construct(
            sensor: $sensor,
            baseReadingTypeID: $baseReadingTypeID,
            currentReading: $currentReading,
            highReading: $highReading,
            lowReading: $lowReading,
            constRecord: $constRecorded,
            updated: $updatedAt,
            readingType: Humidity::READING_TYPE,
        );
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getHumidityID(): int
    {
        return $this->humidityID;
    }
}
