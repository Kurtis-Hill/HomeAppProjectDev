<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

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
