<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use DateTime;
use DateTimeInterface;

interface OutOfBoundsEntityInterface
{
    /**
     * @return int
     */
    public function getOutOfRangeID(): int;

    /**
     * @param int $outOfRangeID
     */
    public function setOutOfRangeID(int $outOfRangeID): void;

    /**
     * @return int|float
     */
    public function getSensorReading(): int|float;

    /**
     * @param float $sensorReading
     */
    public function setSensorReading(float $sensorReading): void;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(): void;

    /**
     * @return AllSensorReadingTypeInterface
     */
    public function getSensorReadingID(): AllSensorReadingTypeInterface;

    /**
     * @param StandardReadingSensorInterface $sensorReadingTypeID
     */
    public function setSensorReadingID(StandardReadingSensorInterface $sensorReadingTypeID): void;

}
