<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;

interface UpdateCurrentSensorFormReadingInterface
{
    public function handleUpdateSensorCurrentReading(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool;
}
