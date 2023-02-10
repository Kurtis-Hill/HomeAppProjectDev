<?php


namespace App\Sensors\Entity\ReadingTypes\Interfaces;

use App\Sensors\Entity\Sensor;

interface AllSensorReadingTypeInterface
{
    public function getSensor(): Sensor;

    public function getSensorID(): int;

    public function setSensorID(int $id);

    public function getReadingType(): string;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float|string;

    public function setCurrentReading(int|float|string $currentCurrentReading);
}
