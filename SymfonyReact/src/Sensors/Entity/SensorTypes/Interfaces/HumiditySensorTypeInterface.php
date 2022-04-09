<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\Humidity;

interface HumiditySensorTypeInterface
{
    public function getHumidObject(): Humidity;

    public function setHumidObject(Humidity $humidID): void;

    public function getMaxHumidity(): float|int;

    public function getMinHumidity(): float|int;
}
