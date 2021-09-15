<?php

namespace App\Services\ESPDeviceSensor\SensorData\Interfaces;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $sensorData, Devices $device): bool;
}
