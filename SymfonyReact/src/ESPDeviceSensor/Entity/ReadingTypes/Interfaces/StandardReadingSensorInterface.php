<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces;


use App\ESPDeviceSensor\Entity\Sensor;

interface StandardReadingSensorInterface
{
    /**
     * Sensor relational Objects
     */
    public function getSensorNameID(): Sensor;

    public function setSensorNameID(Sensor $id);


    /**
     * Sensor Reading Methods
     */
    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getTime(): \DateTimeInterface;

    public function setCurrentReading(int|float $reading): void;

    public function setHighReading(int|float|string $reading): void;

    public function setLowReading(int|float|string $reading): void;

    public function setTime(?\DateTime $time = null): void;

    /**
     * Sensor Functional Methods
     */
    public function getMeasurementDifferenceHighReading(): int|float;

    public function getMeasurementDifferenceLowReading(): int|float;
}
