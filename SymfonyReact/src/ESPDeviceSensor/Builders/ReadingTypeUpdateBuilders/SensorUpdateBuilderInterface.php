<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateReadingTypeCurrentReadingDTO;
use App\ESPDeviceSensor\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;

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
