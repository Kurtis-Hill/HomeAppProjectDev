<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\ESPDeviceSensor\Entity\Sensors;

interface SensorReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensors $sensor): bool;
}
