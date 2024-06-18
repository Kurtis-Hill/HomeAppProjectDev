<?php

namespace App\DTOs\Sensor\Response\SensorReadingTypeResponse\Standard;

use App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO;

interface StandardReadingTypeResponseInterface
{
    public function getSensor(): SensorResponseDTO;

    public function getCurrentReading(): float|int|string|bool;

    public function getHighReading(): float|int|string;

    public function getLowReading(): float|int|string;

    public function getConstRecord(): bool;

    public function getUpdatedAt(): string;
}
