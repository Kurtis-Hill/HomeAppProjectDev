<?php

namespace App\Sensors\DTO\Sensor\CurrentReadingDTO;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateReadingTypeCurrentReadingDTO
{
    private string $currentReading;

    private string $newCurrentReading;

    private AllSensorReadingTypeInterface $sensorReadingObject;

    public function __construct(
        string $newCurrentReading,
        string $currentReading,
        AllSensorReadingTypeInterface $sensorObject,
    )
    {
        $this->currentReading = $newCurrentReading;
        $this->newCurrentReading = $currentReading;
        $this->sensorReadingObject = $sensorObject;
    }

    public function getNewCurrentReading(): string
    {
        return $this->newCurrentReading;
    }

    public function getSensorReadingObject(): AllSensorReadingTypeInterface
    {
        return $this->sensorReadingObject;
    }

    /**
     * @return string
     */
    public function getCurrentReading(): string
    {
        return $this->currentReading;
    }
}
