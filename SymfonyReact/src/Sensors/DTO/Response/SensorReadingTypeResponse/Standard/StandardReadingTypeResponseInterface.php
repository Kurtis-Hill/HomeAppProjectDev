<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard;

use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;

interface StandardReadingTypeResponseInterface
{
    public function getSensor(): SensorResponseDTO;

    public function getCurrentReading(): float|int|string|bool;

    public function getHighReading(): float|int|string;

    public function getLowReading(): float|int|string;

    public function getConstRecord(): bool;

    public function getUpdatedAt(): string;
}
