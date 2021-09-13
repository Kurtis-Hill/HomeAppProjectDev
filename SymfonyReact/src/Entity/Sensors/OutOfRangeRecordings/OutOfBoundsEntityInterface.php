<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;

use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use DateTime;

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
    public function getTime(): \DateTime;

    /**
     * @param DateTime|null $time
     */
    public function setTime(?DateTime $time = null): void;

    /**
     * @return AllSensorReadingTypeInterface
     */
    public function getSensorReadingTypeID(): AllSensorReadingTypeInterface;

    /**
     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void;

}
