<?php

namespace App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders;

use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\LDR;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
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
