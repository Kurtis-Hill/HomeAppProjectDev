<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Devices\Entity\Devices;

interface UpdateBoundaryReadingsInterface
{
    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void;
}
