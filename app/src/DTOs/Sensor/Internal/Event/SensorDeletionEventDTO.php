<?php

declare(strict_types=1);

namespace App\DTOs\Sensor\Internal\Event;

readonly class SensorDeletionEventDTO
{
    public function __construct(
        private string $sensorType,
        private int $deviceID,
    ) {
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }
}
