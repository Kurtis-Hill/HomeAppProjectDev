<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\RelayQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\GenericRelay;

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
