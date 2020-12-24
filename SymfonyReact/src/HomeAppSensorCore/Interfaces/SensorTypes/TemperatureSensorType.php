<?php

namespace App\HomeAppSensorCore\Interfaces\SensorTypes;

use App\Entity\Sensors\ReadingTypes\Temperature;

interface TemperatureSensorType
{
    public function getTempObject(): Temperature;

    public function setTempObject(Temperature $tempID): void;
}
