<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

interface LatitudeReadingTypeInterface
{
    public function getMaxLatitude(): float|int;

    public function getMinLatitude(): float|int;
}
