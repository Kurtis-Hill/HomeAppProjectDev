<?php


namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;

interface AnalogSensorTypeInterface
{
    public function getAnalogObject(): Analog;

    public function setAnalogObject(Analog $tempID): void;

    public function getMaxAnalog(): float|int;

    public function getMinAnalog(): float|int;
}
