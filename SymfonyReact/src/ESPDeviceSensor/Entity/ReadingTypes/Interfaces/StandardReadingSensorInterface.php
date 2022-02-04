<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces;


use App\ESPDeviceSensor\Entity\Sensor;

interface StandardReadingSensorInterface
{
    /**
     * Sensor relational Objects
     */
    public function getSensorNameID(): Sensor;

    public function setSensorObject(Sensor $id);


    /**
     * Sensor Reading Methods
     */
    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setCurrentReading(int|float $reading): void;

    public function setHighReading(int|float|string $reading): void;

    public function setLowReading(int|float|string $reading): void;

    public function setUpdatedAt(): void;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float;

    public function isReadingOutOfBounds(): bool;
    /**
     * Sensor Functional Methods
     */
    public function getMeasurementDifferenceHighReading(): int|float;

    public function getMeasurementDifferenceLowReading(): int|float;

    public function getReadingType(): string;
}
