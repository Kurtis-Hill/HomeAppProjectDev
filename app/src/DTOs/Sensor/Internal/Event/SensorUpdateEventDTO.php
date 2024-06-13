<?php

namespace App\DTOs\Sensor\Internal\Event;

use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorUpdateEventDTO
{
    public function __construct(
        private array $sensorUpdateRequestDTOs,
    ) {}

    #[ArrayShape([SensorUpdateRequestDTOInterface::class])]
    public function getSensorUpdateRequestDTOs(): array
    {
        return $this->sensorUpdateRequestDTOs;
    }
}
