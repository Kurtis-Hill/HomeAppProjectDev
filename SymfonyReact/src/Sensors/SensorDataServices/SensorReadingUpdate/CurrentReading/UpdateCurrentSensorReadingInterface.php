<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateSensorCurrentReading(UpdateSensorCurrentReadingConsumerMessageDTO $updateSensorCurrentReadingConsumerDTO, Devices $device): bool;
}
