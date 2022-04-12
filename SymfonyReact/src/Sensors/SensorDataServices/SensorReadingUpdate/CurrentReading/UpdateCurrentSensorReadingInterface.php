<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;

interface UpdateCurrentSensorReadingInterface
{
    public function handleUpdateSensorCurrentReading(UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO, Devices $device): bool;
}
