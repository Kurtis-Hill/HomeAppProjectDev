<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Devices\Entity\Devices;

interface UpdateBoundaryReadingsInterface
{
    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void;
}
