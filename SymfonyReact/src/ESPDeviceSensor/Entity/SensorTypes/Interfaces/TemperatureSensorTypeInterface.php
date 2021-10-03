<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;

interface TemperatureSensorTypeInterface
{
    public function getTempObject(): Temperature;

    public function setTempObject(Temperature $tempID): void;
}
