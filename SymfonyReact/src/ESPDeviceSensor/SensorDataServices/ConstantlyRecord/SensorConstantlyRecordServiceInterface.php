<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

interface SensorConstantlyRecordServiceInterface
{
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void;
}
