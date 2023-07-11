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
        private string $userType,
        private int $usersID,
    ) {}

    public function getSensorID()
    {
        return $this->sensorID;
    }

    public function getReadingTypeCurrentReadingDTO(): BoolCurrentReadingUpdateRequestDTO
    {
        return $this->readingTypeCurrentReadingDTO;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getUsersID(): int
    {
        return $this->usersID;
    }
}
