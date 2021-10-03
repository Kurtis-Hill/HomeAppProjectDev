<?php


namespace App\ESPDeviceSensor\Entity\ReadingTypes;

interface AllSensorReadingTypeInterface
{
    public function getSensorID(): int;

    public function setSensorID(int $id);

    public function getSensorTypeName(): string;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float;

    public function isReadingOutOfBounds(): bool;
}
