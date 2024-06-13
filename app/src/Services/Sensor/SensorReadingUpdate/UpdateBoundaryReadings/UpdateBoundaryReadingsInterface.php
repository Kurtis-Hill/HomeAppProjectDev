<?php

namespace App\Services\Sensor\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Entity\Device\Devices;

interface UpdateBoundaryReadingsInterface
{
    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void;
}
