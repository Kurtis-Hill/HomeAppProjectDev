<?php

namespace App\DTOs\Sensor\Response\SensorReadingTypeResponse\Standard;

use App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO;
use App\Entity\Sensor\AbstractSensorType;
use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly abstract class AbstractStandardResponseDTO
{
    public function __construct(
        private SensorResponseDTO $sensor,
        private int $baseReadingTypeID,
        private float $currentReading,
        private float $highReading,
        private float $lowReading,
        private bool $constRecord,
        private string $updated,
        private string $readingType,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getBaseReadingTypeID(): int
    {
        return $this->baseReadingTypeID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
//        RequestTypeEnum::ONLY->value,
//        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensor(): SensorResponseDTO
    {
        return $this->sensor;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCurrentReading(): float|int|string|bool
    {
        return $this->currentReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getHighReading(): float|int|string
    {
        return $this->highReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getLowReading(): float|int|string
    {
        return $this->lowReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUpdatedAt(): string
    {
        return $this->updated;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorType(): string
    {
        return AbstractSensorType::STANDARD_READING_SENSOR_TYPE;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getReadingType(): string
    {
        return $this->readingType;
    }
}
