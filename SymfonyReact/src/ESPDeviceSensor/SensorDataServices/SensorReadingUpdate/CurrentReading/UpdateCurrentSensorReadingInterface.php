<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool;
}
