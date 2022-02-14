<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateSensorCurrentReading(UpdateSensorCurrentReadingConsumerMessageDTO $updateSensorCurrentReadingConsumerDTO, Devices $device): bool;
}
