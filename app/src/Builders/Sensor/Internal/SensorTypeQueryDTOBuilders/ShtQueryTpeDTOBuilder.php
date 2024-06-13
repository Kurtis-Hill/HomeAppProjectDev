<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Sht;
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
