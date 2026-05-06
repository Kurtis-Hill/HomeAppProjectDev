<?php

namespace App\DTOs\Sensor\Response\SensorReadingTypeResponse;

interface AllSensorReadingTypeResponseDTOInterface
{
    public function getCurrentReading(): float|int|string|bool;
}
