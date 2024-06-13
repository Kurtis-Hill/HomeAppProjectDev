<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
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
