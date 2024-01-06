<?php


namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\Sensor;
use DateTimeImmutable;
use DateTimeInterface;

interface AllSensorReadingTypeInterface
{
    public function getSensor(): Sensor;

    public function getSensorID(): int;

//    public function setSensorID(int $id);

//    public function getReadingType(): string;

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
