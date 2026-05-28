<?php

namespace App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType;

use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Services\Request\RequestTypeEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class StandardBoundaryReadingsTypeResponseDTO implements BoundaryReadingTypeResponseInterface
{
    private int $sensorReadingTypeID;

    private string $readingType;

    private int|float|string $highReading;

    private int|float|string $lowReading;

    private int|float|string $constRecord;

    private int $outOfBoundsAlertTimer;

    public function __construct(
        int $sensorReadingTypeID,
        string $readingType,
        int|float|string $highReading,
        int|float|string $lowReading,
        int|float|string $constRecord,
        int $outOfBoundsAlertTimer
    ) {
        $this->sensorReadingTypeID = $sensorReadingTypeID;
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
        $this->outOfBoundsAlertTimer = $outOfBoundsAlertTimer;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorReadingTypeID(): int
    {
        return $this->sensorReadingTypeID;
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

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getHighReading(): int|float|string
    {
        return $this->highReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getLowReading(): int|float|string
    {
        return $this->lowReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getConstRecord(): int|float|string
    {
        return $this->constRecord;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getOutOfBoundsAlertTimer(): int
    {
        return $this->outOfBoundsAlertTimer;
    }
}
