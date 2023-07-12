<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;

interface RequestSensorCurrentReadingHandlerInterface
{
    public function handleUpdateSensor(RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO): bool;
}
