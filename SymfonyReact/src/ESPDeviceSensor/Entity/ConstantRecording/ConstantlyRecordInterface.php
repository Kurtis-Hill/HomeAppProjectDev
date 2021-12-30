<?php

namespace App\ESPDeviceSensor\Entity\ConstantRecording;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
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

    public function getSensorReadingTypeID();

//    /**
//     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
//     */
//    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void;
}
