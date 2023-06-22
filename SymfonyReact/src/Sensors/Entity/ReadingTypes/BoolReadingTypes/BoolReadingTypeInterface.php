<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\Sensor;
use DateTimeInterface;

interface BoolReadingTypeInterface
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

    public function setUpdatedAt(DateTimeInterface $updatedAt);

}
