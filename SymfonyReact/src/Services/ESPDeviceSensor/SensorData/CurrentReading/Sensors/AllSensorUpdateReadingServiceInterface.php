<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use Doctrine\Common\Collections\ArrayCollection;

interface AllSensorUpdateReadingServiceInterface
{
    public function updateCurrentReading(
        UpdateSensorReadingDTO $updateSensorReadingDTO,
        ArrayCollection $sensorReadingTypeObjects
    ): bool;
}
