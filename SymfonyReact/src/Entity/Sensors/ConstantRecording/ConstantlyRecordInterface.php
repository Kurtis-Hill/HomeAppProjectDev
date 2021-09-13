<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\ReadingTypes\Analog;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use DateTime;

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

    /**
     * @return DateTime
     */
    public function getTime(): DateTime;

    /**
     * @param DateTime|null $time
     */
    public function setTime(?DateTime $time = null): void;

    /**
     * @return Analog
     */
    public function getSensorReadingTypeID(): AllSensorReadingTypeInterface;

    /**
     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void;
}
