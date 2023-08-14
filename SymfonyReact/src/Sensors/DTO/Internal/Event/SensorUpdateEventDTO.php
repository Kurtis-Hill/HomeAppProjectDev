<?php

namespace App\Sensors\DTO\Internal\Event;

use App\Sensors\Entity\Sensor;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorUpdateEventDTO
{
    public function __construct(
        private array $sensorIDs,
    ) {}

    #[ArrayShape(['int'])]
    public function getSensorIDs(): array
    {
        return $this->sensorIDs;
    }
}
