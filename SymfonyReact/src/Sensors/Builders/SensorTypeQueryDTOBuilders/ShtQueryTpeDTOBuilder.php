<?php

namespace App\Sensors\Builders\SensorTypeQueryDTOBuilders;

use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Sht;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class ShtQueryTpeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder;

    private HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder;

    public function __construct(
        TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder,
        HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder
    ) {
        $this->temperatureQueryTypeDTOBuilder = $temperatureQueryTypeDTOBuilder;
        $this->humidityQueryTypeDTOBuilder = $humidityQueryTypeDTOBuilder;
    }

    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            Sht::ALIAS,
            Sht::class,
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    #[ArrayShape([JoinQueryDTO::class])]
    public function buildSensorReadingTypes(): array
    {
        return [
            $this->temperatureQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
            $this->humidityQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }


    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            Sht::ALIAS,
            $sensorTypeID
        );
    }
}
