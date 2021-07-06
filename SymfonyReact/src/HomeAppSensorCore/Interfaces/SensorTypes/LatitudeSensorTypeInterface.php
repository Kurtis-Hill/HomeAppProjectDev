<?php


namespace App\HomeAppSensorCore\Interfaces\SensorTypes;


use App\Entity\Sensors\ReadingTypes\Latitude;

interface LatitudeSensorTypeInterface
{
    public function getLatitudeObject(): Latitude;

    public function setLatitudeObject(Latitude $tempID): void;
}
