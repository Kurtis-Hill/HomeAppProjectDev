<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;


use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;

interface LatitudeSensorTypeInterface
{
    public function getLatitudeObject(): Latitude;

    public function setLatitudeObject(Latitude $tempID): void;

    public function getMaxLatitude(): float|int;

    public function getMinLatitude(): float|int;
}
