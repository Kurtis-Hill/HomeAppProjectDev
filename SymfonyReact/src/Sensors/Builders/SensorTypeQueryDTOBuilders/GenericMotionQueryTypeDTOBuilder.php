<?php

namespace App\Sensors\Builders\SensorTypeQueryDTOBuilders;

use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\MotionQueryTypeDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;

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
