<?php


namespace App\HomeAppSensorCore\Interfaces\SensorTypes;


use App\Entity\Sensors\ReadingTypes\Analog;

interface AnalogSensorType
{
    public function getAnalogObject(): Analog;

    public function setAnalogObject(Analog $tempID): void;
}
