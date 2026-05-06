<?php

namespace App\DTOs\Sensor\Response\SensorReadingTypeResponse\Bool;

interface BoolReadingTypeResponseInterface
{
    public function getCurrentReading(): bool;
}
