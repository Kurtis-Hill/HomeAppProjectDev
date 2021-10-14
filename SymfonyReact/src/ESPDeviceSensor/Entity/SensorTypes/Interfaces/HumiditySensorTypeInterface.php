<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;

interface HumiditySensorTypeInterface
{
    public function getHumidObject(): Humidity;

    public function setHumidObject(Humidity $tempID): void;
}
