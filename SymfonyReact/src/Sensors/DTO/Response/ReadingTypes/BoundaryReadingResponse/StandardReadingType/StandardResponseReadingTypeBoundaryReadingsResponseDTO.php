<?php

namespace App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType;

class StandardResponseReadingTypeBoundaryReadingsResponseDTO implements ReadingTypeBoundaryReadingResponseInterface
{
    private int $sensorReadingTypeID;

    private string $readingType;

    private int|float|string $highReading;

    private int|float|string $lowReading;

    private int|float|string $constRecord;

    public function __construct(
        int $sensorReadingTypeID,
        string $readingType,
        int|float|string $highReading,
        int|float|string $lowReading,
        int|float|string $constRecord
    ) {
        $this->sensorReadingTypeID = $sensorReadingTypeID;
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
    }

    public function getSensorReadingTypeID(): int
    {
        return $this->sensorReadingTypeID;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getHighReading(): int|float|string
    {
        return $this->highReading;
    }

    public function getLowReading(): int|float|string
    {
        return $this->lowReading;
    }

    public function getConstRecord(): int|float|string
    {
        return $this->constRecord;
    }
}
