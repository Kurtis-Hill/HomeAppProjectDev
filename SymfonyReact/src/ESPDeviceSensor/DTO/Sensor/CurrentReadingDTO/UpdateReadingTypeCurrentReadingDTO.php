<?php

namespace App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateReadingTypeCurrentReadingDTO
{
    private string $currentReading;

    private string $newCurrentReading;

    private AllSensorReadingTypeInterface $sensorReadingObject;

    public function __construct(
        string $currentReading,
        string $newCurrentReading,
        AllSensorReadingTypeInterface $sensorObject,
    )
    {
        $this->newCurrentReading = $currentReading;
        $this->currentReading = $newCurrentReading;
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