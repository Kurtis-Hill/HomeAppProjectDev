<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;

interface SensorUpdateBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function setNewBoundaryForReadingType(SensorTypeInterface $sensorTypeObject, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void;

    /**
     * @throws ReadingTypeObjectBuilderException
     */
    public function buildUpdateSensorBoundaryReadingsDTO(array $sensorData, SensorTypeInterface $sensorTypeObject): UpdateSensorBoundaryReadingsDTO;
}
