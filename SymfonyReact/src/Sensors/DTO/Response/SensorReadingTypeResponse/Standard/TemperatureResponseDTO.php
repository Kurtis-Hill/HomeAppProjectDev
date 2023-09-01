<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class TemperatureResponseDTO extends AbstractStandardResponseDTO implements AllSensorReadingTypeResponseDTOInterface, StandardReadingTypeResponseInterface, SensorReadingTypeResponseDTOInterface
{
    public function __construct(
        private int $temperatureID,
        SensorResponseDTO $sensor,
        float $currentReading,
        float $highReading,
        float $lowReading,
        bool $constRecorded,
        string $updatedAt
    ) {
        parent::__construct(
            sensor: $sensor,
            currentReading: $currentReading,
            highReading: $highReading,
            lowReading: $lowReading,
            constRecord: $constRecorded,
            updated: $updatedAt,
            readingType: Temperature::READING_TYPE,
        );
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getTemperatureID(): int
    {
        return $this->temperatureID;
    }
}
