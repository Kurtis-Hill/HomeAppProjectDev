<?php


namespace App\Entity\Sensor\SensorTypes\Interfaces;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;
use DateTimeInterface;

interface AllSensorReadingTypeInterface
{
    public function getReadingTypeID(): int;

    public function getSensor(): Sensor;

    public function getSensorID(): int;

    public function getConstRecord(): bool;

    public function setConstRecord(bool $constRecord);

    public function getCurrentReading(): int|float|string|bool;

    public function setCurrentReading(int|float|string $currentReading);

    public static function getReadingTypeName(): string;

    public function getReadingType(): string;

    public function setUpdatedAt(): void;

    public function setCreatedAt(?DateTimeInterface $dateTime = null): void;

    public function getUpdatedAt(): DateTimeInterface;

    public function getBaseReadingType(): BaseSensorReadingType;
}
