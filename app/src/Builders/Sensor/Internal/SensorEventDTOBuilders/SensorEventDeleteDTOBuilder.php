<?php

declare(strict_types=1);

namespace App\Builders\Sensor\Internal\SensorEventDTOBuilders;

use App\DTOs\Sensor\Internal\Event\SensorDeletionEventDTO;
use App\Entity\Sensor\Sensor;

class SensorEventDeleteDTOBuilder
{
    public function buildSensorDeletionEventDTO(string $sensorType, int $deviceID): SensorDeletionEventDTO
    {
        return new SensorDeletionEventDTO($sensorType, $deviceID);
    }
}
