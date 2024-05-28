<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;

interface TemperatureReadingTypeInterface
{
    public function getMaxTemperature(): float|int;

    public function getMinTemperature(): float|int;
}
