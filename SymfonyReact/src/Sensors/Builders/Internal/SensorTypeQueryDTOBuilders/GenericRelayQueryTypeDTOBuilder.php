<?php

namespace App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders;

use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\RelayQueryTypeDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;

class GenericRelayQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private RelayQueryTypeDTOBuilder $relayQueryTypeDTOBuilder;

    public function __construct(RelayQueryTypeDTOBuilder $relayQueryTypeDTOBuilder)
    {
        $this->relayQueryTypeDTOBuilder = $relayQueryTypeDTOBuilder;
    }

    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            GenericRelay::ALIAS,
            GenericRelay::class,
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }

    public function buildSensorReadingTypes(): array
    {
        return [
            $this->relayQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }

    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            GenericRelay::ALIAS,
            $sensorTypeID,
        );
    }
}
