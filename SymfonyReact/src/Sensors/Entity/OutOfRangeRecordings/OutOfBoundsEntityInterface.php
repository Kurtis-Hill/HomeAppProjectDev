<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use DateTime;
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
