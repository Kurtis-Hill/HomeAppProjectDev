<?php


namespace App\HomeAppSensorCore\Interfaces\SensorTypes;


use App\Entity\Sensors\ReadingTypes\Humidity;

interface HumiditySensorType
{
    public function getHumidObject(): Humidity;

    public function setHumidObject(Humidity $tempID): void;
}
