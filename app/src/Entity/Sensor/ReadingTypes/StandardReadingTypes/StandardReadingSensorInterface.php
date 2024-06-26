<?php

namespace App\Entity\Sensor\ReadingTypes\StandardReadingTypes;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;

interface StandardReadingSensorInterface
{
    /**
     * Sensor relational Objects
     */
    public function getSensor(): Sensor;

    public function setSensor(Sensor $id);

    /**
     * Sensor Reading Methods
     */
    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setCurrentReading(int|float $currentReading): void;

    public function setHighReading(int|float $reading): void;

    public function setLowReading(int|float $reading): void;

    public function setUpdatedAt(): void;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float;

    public function isReadingOutOfBounds(): bool;
    /**
     * Sensor Functional Methods
     */
    public function getMeasurementDifferenceHighReading(): int|float;

    public function getMeasurementDifferenceLowReading(): int|float;

    public function getReadingType(): string;

    public static function getReadingTypeName(): string;

    public function getBaseReadingType(): BaseSensorReadingType;

    public function setBaseReadingType(BaseSensorReadingType $readingType): void;
}
