<?php

namespace App\Sensors\Builders\SensorUpdateRequestDTOBuilder;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\BusSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\RegularSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateDataEncapsulationDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use JetBrains\PhpStorm\ArrayShape;

class SensorUpdateDataEncapsulationDTOBuilder
{
    #[ArrayShape(
        [
            Dht::NAME => RegularSensorUpdateRequestDTO::class,
            Bmp::NAME => RegularSensorUpdateRequestDTO::class,
            GenericMotion::NAME => RegularSensorUpdateRequestDTO::class,
            GenericRelay::NAME => RegularSensorUpdateRequestDTO::class,
            Dallas::NAME => BusSensorUpdateRequestDTO::class,
            Soil::NAME => BusSensorUpdateRequestDTO::class,
        ]
    )]
    public static function buildSingleSensorUpdateDataDTORequestShape(Sensor $sensor, SensorUpdateRequestDTOInterface $sensorUpdateRequestDTO): array
    {
        return [
            strtolower($sensor->getSensorTypeObject()->getSensorType()) => $sensorUpdateRequestDTO
        ];
    }

    public static function buildSensorUpdateDataEncapsulationDTO(
        array $sensorData
    ): SensorUpdateDataEncapsulationDTO {
        return new SensorUpdateDataEncapsulationDTO(
            $sensorData
        );
    }
}
