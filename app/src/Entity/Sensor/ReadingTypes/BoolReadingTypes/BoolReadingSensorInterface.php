<?php

namespace App\Entity\Sensor\ReadingTypes\BoolReadingTypes;

use App\Entity\Sensor\Sensor;
use DateTimeInterface;

interface BoolReadingSensorInterface
{
    public function getBoolID(): int;

    public function getSensor(): Sensor;

    public function setSensor(Sensor $sensor);

    public function getCurrentReading(): bool;

    public function setCurrentReading(bool $currentReading);

    public function getRequestedReading(): bool;

    public function setRequestedReading(bool $requestedReading);

    public function getExpectedReading(): ?bool;

    public function setExpectedReading(?bool $expectedReading);

//    public function getReadingType(): string;

//    public function setBoolReadingType(string $sensorType);

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt);

    public function getUpdatedAt(): DateTimeInterface;

    public function setUpdatedAt(): void;

    public function getConstRecord(): bool;

    public static function getReadingTypeName(): string;
}
