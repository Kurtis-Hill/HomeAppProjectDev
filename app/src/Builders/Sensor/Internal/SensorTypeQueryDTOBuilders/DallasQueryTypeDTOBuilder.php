<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Dallas;
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
            'sensor',
            'sensorID',
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
