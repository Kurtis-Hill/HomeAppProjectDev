<?php

namespace App\Sensors\Builders\SensorTypeQueryDTOBuilders;

use App\Sensors\Entity\SensorType;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

interface SensorTypeQueryDTOBuilderInterface
{
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO;

    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO;

    #[Pure]
    #[ArrayShape([JoinQueryDTO::class])]
    public function buildSensorReadingTypes(): array;
}