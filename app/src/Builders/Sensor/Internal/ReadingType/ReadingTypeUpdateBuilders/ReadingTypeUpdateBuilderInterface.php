<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeObjectBuilderException;

interface ReadingTypeUpdateBuilderInterface
{
    /**
     * @throws ReadingTypeObjectBuilderException
     * @throws ReadingTypeNotExpectedException
     */
    public function buildReadingTypeCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        AbstractCurrentReadingUpdateRequestDTO $sensorData
    ): ReadingTypeUpdateCurrentReadingDTO;
}
