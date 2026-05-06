<?php

namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface TemperatureReadingTypeInterface
{
    public function getMaxTemperature(): float|int;

    public function getMinTemperature(): float|int;
}
