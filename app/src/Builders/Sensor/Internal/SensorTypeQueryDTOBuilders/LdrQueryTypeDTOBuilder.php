<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\LDR;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class LdrQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder;

    public function __construct(
        AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder,
    ) {
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
            LDR::ALIAS,
            LDR::class,
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            LDR::ALIAS,
            $sensorTypeID
        );
    }
}
