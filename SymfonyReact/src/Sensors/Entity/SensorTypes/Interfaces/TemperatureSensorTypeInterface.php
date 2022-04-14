<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\Temperature;

interface TemperatureSensorTypeInterface
{
    public function getTempObject(): Temperature;

    public function setTempObject(Temperature $tempID): void;

    public function getMaxTemperature(): float|int;

    public function getMinTemperature(): float|int;
}
