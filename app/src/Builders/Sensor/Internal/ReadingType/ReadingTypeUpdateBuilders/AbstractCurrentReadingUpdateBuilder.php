<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

abstract class AbstractCurrentReadingUpdateBuilder
{
    protected function buildSensorUpdateCurrentReadingDTO(
        AllSensorReadingTypeInterface $standardReadingSensor,
        string $newSensorReading,
    ): ReadingTypeUpdateCurrentReadingDTO {
        return new ReadingTypeUpdateCurrentReadingDTO(
            $newSensorReading,
            $standardReadingSensor->getCurrentReading(),
            $standardReadingSensor
        );
    }
}
