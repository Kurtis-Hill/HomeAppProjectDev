<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use JetBrains\PhpStorm\NoReturn;

interface TemperatureSensorTypeInterface
{
    public function getTempObject(): Temperature;

    #[NoReturn]
    public function setTempObject(Temperature $tempID): void;

    public function getMaxTemperature(): float|int;

    public function getMinTemperature(): float|int;
}
