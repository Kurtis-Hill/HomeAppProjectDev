<?php

namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface HumidityReadingTypeInterface
{
    public function getMaxHumidity(): float|int;

    public function getMinHumidity(): float|int;
}
