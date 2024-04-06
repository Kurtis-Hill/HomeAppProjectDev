<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool;

interface BoolReadingTypeResponseInterface
{
    public function getCurrentReading(): bool;
}
