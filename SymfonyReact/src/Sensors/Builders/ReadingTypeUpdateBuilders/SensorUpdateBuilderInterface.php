<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\AbstractCurrentReadingUpdateRequest;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

interface SensorUpdateBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function buildUpdateSensorBoundaryReadingsDTO(
        array $sensorData,
        AllSensorReadingTypeInterface $sensorReadingTypeObject
    ): UpdateStandardSensorBoundaryReadingsDTO;

    /**
     * @throws ReadingTypeObjectBuilderException
     * @throws ReadingTypeNotExpectedException
     */
    public function buildCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        array $sensorData
    ): ReadingTypeUpdateCurrentReadingDTO;

    public function buildRequestCurrentReadingUpdateDTO(float $currentReading): AbstractCurrentReadingUpdateRequest;
}
