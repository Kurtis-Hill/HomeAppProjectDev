<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateReadingTypeCurrentReadingDTO;
use App\Sensors\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

interface SensorUpdateBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function setNewBoundaryForReadingType(
        SensorTypeInterface $sensorTypeObject,
        UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void;

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
    ): UpdateReadingTypeCurrentReadingDTO;
}
