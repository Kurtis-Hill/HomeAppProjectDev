<?php

namespace App\Sensors\DTO\Internal\Event;

use App\Sensors\Entity\Sensor;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorUpdateEventDTO
{
    public function __construct(
        private int $sensor,
    ) {}

    public function getSensorID(): int
    {
        return $this->sensor;
    }
}
