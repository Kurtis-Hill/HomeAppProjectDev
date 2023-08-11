<?php

namespace App\Sensors\DTO\Internal\Event;

use App\Sensors\Entity\Sensor;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorUpdateEventDTO
{
    public function __construct(
        private Sensor $sensor,
    ) {}

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }
}
