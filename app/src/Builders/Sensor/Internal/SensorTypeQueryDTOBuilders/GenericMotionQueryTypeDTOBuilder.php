<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\MotionQueryTypeDTOBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\GenericMotion;

class GenericMotionQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    private MotionQueryTypeDTOBuilder $genericMotionQueryTypeDTOBuilder;

    public function __construct(MotionQueryTypeDTOBuilder $genericMotionQueryTypeDTOBuilder)
    {
        $this->genericMotionQueryTypeDTOBuilder = $genericMotionQueryTypeDTOBuilder;
    }

    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            GenericMotion::ALIAS,
            GenericMotion::class,
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }

    public function buildSensorReadingTypes(): array
    {
        return [
            $this->genericMotionQueryTypeDTOBuilder->buildReadingTypeJoinQueryDTO(),
        ];
    }

    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            GenericMotion::ALIAS,
            $sensorTypeID,
        );
    }
}
