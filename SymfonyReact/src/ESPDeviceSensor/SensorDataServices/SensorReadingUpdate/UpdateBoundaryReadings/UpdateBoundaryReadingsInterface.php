<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Entity\Devices\Devices;

interface UpdateBoundaryReadingsInterface
{
    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void;
}
