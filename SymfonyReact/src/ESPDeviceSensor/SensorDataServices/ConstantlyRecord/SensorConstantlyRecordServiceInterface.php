<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;

interface SensorConstantlyRecordServiceInterface
{
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingType): void;
}
