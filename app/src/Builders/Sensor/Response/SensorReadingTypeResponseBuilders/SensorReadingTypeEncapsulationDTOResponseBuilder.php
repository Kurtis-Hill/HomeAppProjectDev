<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\SensorReadingTypeEncapsulationResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class SensorReadingTypeEncapsulationDTOResponseBuilder
{
    public static function buildSensorReadingTypeEncapsulationDTOs(
        #[ArrayShape([AllSensorReadingTypeResponseDTOInterface::class])]
        array $sensorReadingTypeResponseDTOs,
        string $sensorType,
    ): SensorReadingTypeEncapsulationResponseDTO {
        return new SensorReadingTypeEncapsulationResponseDTO(
            $sensorReadingTypeResponseDTOs,
            $sensorType,
        );
    }
}
