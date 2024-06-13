<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Bmp;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class BmpQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder;

    private HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder;

    private LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder;

    public function __construct(
        TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder,
        HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder,
        LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder
    ) {
        $this->temperatureQueryTypeDTOBuilder = $temperatureQueryTypeDTOBuilder;
        $this->humidityQueryTypeDTOBuilder = $humidityQueryTypeDTOBuilder;
        $this->latitudeQueryTypeDTOBuilder = $latitudeQueryTypeDTOBuilder;
    }

    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            Bmp::ALIAS,
            Bmp::class,
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
            $this->latitudeQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            Bmp::ALIAS,
            $sensorTypeID
        );
    }
}
