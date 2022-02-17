<?php


namespace App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces;

use App\ESPDeviceSensor\Entity\Sensor;

interface AllSensorReadingTypeInterface
{
    public function getSensorNameID(): Sensor;

    public function getSensorID(): int;

    public function setSensorID(int $id);

    public function getReadingType(): string;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float|string;

    public function setCurrentReading(int|float|string $currentReading);
}
