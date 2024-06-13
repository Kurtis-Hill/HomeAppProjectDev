<?php

namespace App\DTOs\Sensor\Request\OutOfBounds\Elastic;

use DateTimeInterface;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class OutOfBoundsElasticPersistenceDTO
{
    private int $sensorReadingID;

    private float $sensorReading;

    private DateTimeInterface $createdAt;

    public function __construct(
        int $sensorReadingID,
        float $sensorReading,
        DateTimeInterface $createdAt
    ) {
        $this->sensorReadingID = $sensorReadingID;
        $this->sensorReading = $sensorReading;
        $this->createdAt = $createdAt;
    }

    public function getSensorReadingID(): int
    {
        return $this->sensorReadingID;
    }

    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
