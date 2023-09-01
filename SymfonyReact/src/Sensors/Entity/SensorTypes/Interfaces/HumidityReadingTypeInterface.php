<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;

interface HumidityReadingTypeInterface
{
    public function getHumidObject(): Humidity;

    public function setHumidObject(Humidity $humidID): void;

    public function getMaxHumidity(): float|int;

    public function getMinHumidity(): float|int;
}
