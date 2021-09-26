<?php

namespace App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord;

use App\Entity\Sensors\ConstantRecording\ConstantlyRecordInterface;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;

interface SensorConstantlyRecordServiceInterface
{
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingType): void;
}
