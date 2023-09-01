<?php

namespace App\Sensors\DTO\Internal\CurrentReadingDTO;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class ReadingTypeUpdateCurrentReadingDTO
{
    public function __construct(
        private string $newCurrentReading,
        private string $currentReading,
        private AllSensorReadingTypeInterface $sensorObject,
    ) {
    }

    public function getNewCurrentReading(): string
    {
        return $this->newCurrentReading;
    }

    public function getSensorReadingObject(): AllSensorReadingTypeInterface
    {
        return $this->sensorObject;
    }

    public function getCurrentReading(): string
    {
        return $this->currentReading;
    }
}
