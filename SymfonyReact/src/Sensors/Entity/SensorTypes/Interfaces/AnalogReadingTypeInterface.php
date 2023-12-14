<?php


namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;

interface AnalogReadingTypeInterface
{
    public function getMaxAnalog(): float|int;
    public function getMinAnalog(): float|int;
}
