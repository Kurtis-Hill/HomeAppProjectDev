<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

interface SensorReadingTypesValidatorServiceInterface
{
    public function validateSensorTypeObject(SensorTypeInterface $sensorTypeObject): array;

    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array;
}
