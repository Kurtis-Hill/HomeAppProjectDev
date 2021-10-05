<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;

interface SensorConstantlyRecordServiceInterface
{
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingType): void;
}
