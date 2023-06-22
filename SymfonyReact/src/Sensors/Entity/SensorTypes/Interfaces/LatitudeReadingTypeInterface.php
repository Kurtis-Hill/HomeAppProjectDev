<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;


use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;

interface LatitudeReadingTypeInterface
{
    public function getLatitudeObject(): Latitude;

    public function setLatitudeObject(Latitude $latitudeID): void;

    public function getMaxLatitude(): float|int;

    public function getMinLatitude(): float|int;
}
