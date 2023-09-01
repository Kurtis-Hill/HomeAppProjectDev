<?php


namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\Analog;

interface AnalogSensorTypeInterface
{
    public function getAnalogObject(): Analog;

    public function setAnalogObject(Analog $analogID): void;

    public function getMaxAnalog(): float|int;

    public function getMinAnalog(): float|int;
}
