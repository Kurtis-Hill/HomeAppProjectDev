<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use JetBrains\PhpStorm\ArrayShape;

readonly class SensorFullResponseDTO
{
    public function __construct(
        private SensorResponseDTO $sensor,
        #[ArrayShape([SensorReadingTypeResponseDTOInterface::class])]
        private array $sensorReadingTypes
    ) {
    }

    public function getSensor(): SensorResponseDTO
    {
        return $this->sensor;
    }

    public function getSensorReadingTypes(): array
    {
        return $this->sensorReadingTypes;
    }
}
