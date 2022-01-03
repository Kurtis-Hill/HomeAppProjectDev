<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorCurrentReadingDTO;

interface UpdateCurrentSensorFormReadingInterface
{
    public function handleUpdateSensorCurrentReading(UpdateSensorCurrentReadingDTO $updateSensorReadingDTO, Devices $device): bool;
}
