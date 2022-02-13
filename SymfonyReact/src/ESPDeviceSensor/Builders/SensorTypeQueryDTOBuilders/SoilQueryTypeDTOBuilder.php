<?php

namespace App\ESPDeviceSensor\Builders\SensorTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class SoilQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder;

    public function __construct(
        AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder,
    )
    {
        $this->analogQueryTypeDTOBuilder = $analogQueryTypeDTOBuilder;
    }

    #[Pure]
    #[ArrayShape([JoinQueryDTO::class])]
    public function buildSensorReadingTypes(): array
    {
        return [
            $this->analogQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }


    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            Soil::ALIAS,
            Soil::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            Soil::ALIAS,
            $sensorTypeID
        );
    }
}
