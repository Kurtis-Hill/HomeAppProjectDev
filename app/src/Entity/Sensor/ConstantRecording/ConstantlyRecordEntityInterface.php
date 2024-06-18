<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use DateTimeInterface;

interface ConstantlyRecordEntityInterface
{
    /**
     * @return int
     */
    public function getConstRecordID(): int;

    /**
     * @param int $constRecordID
     */
    public function setConstRecordID(int $constRecordID): void;

    /**
     * @return float
     */
    public function getSensorReading(): int|float;

    /**
     * @param float $sensorReading
     */
    public function setSensorReading(float $sensorReading): void;

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(): void;

    public function getSensorReadingObject();

    public function setSensorReadingObject(BaseSensorReadingType $sensorReadingTypeID): void;

//    /**
//     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
//     */
//    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void;
}
