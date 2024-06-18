<?php

namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface LatitudeReadingTypeInterface
{
    public function getMaxLatitude(): float|int;

    public function getMinLatitude(): float|int;
}
