<?php


namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;

interface AnalogSensorTypeInterface
{
    public function getAnalogObject(): Analog;

    public function setAnalogObject(Analog $tempID): void;
}
