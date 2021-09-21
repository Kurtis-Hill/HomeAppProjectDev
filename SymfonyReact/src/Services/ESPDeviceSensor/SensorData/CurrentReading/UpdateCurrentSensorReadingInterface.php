<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool;
}
