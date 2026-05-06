<?php

declare(strict_types=1);

namespace App\Builders\Sensor\Request\SensorUpdateBuilders;

use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorDeletionDTO;
use App\Entity\Sensor\Sensor;

readonly class SensorDeletionDTOBuilder
{
    public function buildSensorDeletionDTO(string $sensorType): SensorDeletionDTO
    {
        return new SensorDeletionDTO(
            sensorType: $sensorType
        );
    }
}
