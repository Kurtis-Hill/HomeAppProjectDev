<?php

namespace App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs;

use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class RequestSensorCurrentReadingUpdateMessageDTO
{
    public function __construct(
        private  int $sensorID,
        private BoolCurrentReadingUpdateRequestDTO $readingTypeCurrentReadingDTO,
    ) {}

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    public function getReadingTypeCurrentReadingDTO(): BoolCurrentReadingUpdateRequestDTO
    {
        return $this->readingTypeCurrentReadingDTO;
    }
}
