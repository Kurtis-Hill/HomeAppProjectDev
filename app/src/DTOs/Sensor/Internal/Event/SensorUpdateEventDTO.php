<?php

namespace App\DTOs\Sensor\Internal\Event;

use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorUpdateEventDTO
{
    public function __construct(
        private int $sensorID,
    ) {}

    public function getSensorID(): int
    {
        return $this->sensorID;
    }
}
