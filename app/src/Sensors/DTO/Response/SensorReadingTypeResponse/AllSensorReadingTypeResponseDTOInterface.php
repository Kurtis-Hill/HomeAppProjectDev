<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

interface AllSensorReadingTypeResponseDTOInterface
{
    public function getCurrentReading(): float|int|string|bool;
}
