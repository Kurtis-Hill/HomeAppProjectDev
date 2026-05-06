<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use DateTimeInterface;

interface OutOfBoundsEntityInterface
{
    public function getOutOfRangeID(): int;

    public function setOutOfRangeID(int $outOfRangeID): void;

    public function getSensorReading(): int|float;

    public function setSensorReading(float $sensorReading): void;

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(): void;

    public function getBaseSensorReadingType(): BaseSensorReadingType;

    public function setBaseSensorReadingType(BaseSensorReadingType $sensorReadingTypeID): void;
}
