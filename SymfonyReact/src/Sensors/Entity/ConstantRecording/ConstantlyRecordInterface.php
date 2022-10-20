<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Analog;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

interface ConstantlyRecordInterface
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

    public function setSensorReadingObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void;

//    /**
//     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
//     */
//    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void;
}
