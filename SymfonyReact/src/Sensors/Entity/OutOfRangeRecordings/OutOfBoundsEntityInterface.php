<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
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
     * @return \App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface
     */
    public function getSensorReadingID(): BaseSensorReadingType;

    /**
     * @param \App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface $sensorReadingTypeID
     */
    public function setSensorReadingID(BaseSensorReadingType $sensorReadingTypeID): void;

}
