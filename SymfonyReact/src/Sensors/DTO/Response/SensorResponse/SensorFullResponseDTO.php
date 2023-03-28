<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use JetBrains\PhpStorm\ArrayShape;

readonly class SensorFullResponseDTO
{
    public function __construct(
        private SensorPartialResponseDTO $sensor,
        #[ArrayShape([SensorReadingTypeResponseDTOInterface::class])]
        private array $sensorReadingTypes
    ) {
    }

    public function getSensor(): SensorPartialResponseDTO
    {
        return $this->sensor;
    }

    public function getSensorReadingTypes(): array
    {
        return $this->sensorReadingTypes;
    }
}
