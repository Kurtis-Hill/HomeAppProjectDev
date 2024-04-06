<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

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
