<?php

namespace App\ESPDeviceSensor\Builders\SensorTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DallasQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder;

    public function __construct(TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder)
    {
        $this->temperatureQueryTypeDTOBuilder = $temperatureQueryTypeDTOBuilder;
    }

    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            Dallas::ALIAS,
            Dallas::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            Dallas::ALIAS,
            $sensorTypeID
        );
    }

    #[Pure]
    #[ArrayShape([JoinQueryDTO::class])]
    public function buildSensorReadingTypes(): array
    {
        return [
            $this->temperatureQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }

}
