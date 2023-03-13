<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorFullResponseDTO;

interface StandardReadingTypeResponseInterface
{
    public function getSensor(): SensorFullResponseDTO;

    public function getCurrentReading(): float|int|string;

    public function getHighReading(): float|int|string;

    public function getLowReading(): float|int|string;

    public function getConstRecordedAt(): bool;

    public function getUpdatedAt(): string;
}
