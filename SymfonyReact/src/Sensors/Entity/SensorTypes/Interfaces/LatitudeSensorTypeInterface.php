<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;


use App\Sensors\Entity\ReadingTypes\Latitude;

interface LatitudeSensorTypeInterface
{
    public function getLatitudeObject(): Latitude;

    public function setLatitudeObject(Latitude $latitudeID): void;

    public function getMaxLatitude(): float|int;

    public function getMinLatitude(): float|int;
}
