<?php

namespace App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class RequestSensorCurrentReadingUpdateTransportMessageDTO
{
    public function __construct(
        private int $sensorID,
        private BoolCurrentReadingUpdateDTO $readingTypeCurrentReadingDTO,
    ) {}

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    public function getReadingTypeCurrentReadingDTO(): BoolCurrentReadingUpdateDTO
    {
        return $this->readingTypeCurrentReadingDTO;
    }
}
