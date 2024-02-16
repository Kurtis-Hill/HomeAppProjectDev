<?php

namespace App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeEncapsulationResponseDTO;
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
