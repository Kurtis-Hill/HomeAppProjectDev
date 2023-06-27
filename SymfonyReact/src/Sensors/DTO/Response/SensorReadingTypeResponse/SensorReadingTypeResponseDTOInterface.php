<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

interface SensorReadingTypeResponseDTOInterface
{
    public function getCurrentReading(): float|int|string|bool;
}
