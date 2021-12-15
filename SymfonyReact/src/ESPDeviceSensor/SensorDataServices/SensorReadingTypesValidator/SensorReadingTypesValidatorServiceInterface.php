<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

interface SensorReadingTypesValidatorServiceInterface
{
    public function validateReadingTypeObjects(SensorTypeInterface $sensorTypeObject): array;
}
